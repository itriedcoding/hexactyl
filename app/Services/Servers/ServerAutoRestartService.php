<?php

namespace Hexactyl\Services\Servers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Hexactyl\Models\Server;
use Hexactyl\Repositories\Wings\DaemonServerRepository;

class ServerAutoRestartService
{
    public function __construct(
        private DaemonServerRepository $daemonServerRepository,
    ) {
    }

    /**
     * Enables auto-restart for a server with a cron-based schedule.
     */
    public function enableAutoRestart(Server $server, array $schedule): bool
    {
        $cronExpression = $schedule['cron'] ?? null;
        $enabled = $schedule['enabled'] ?? true;

        if (!$cronExpression || !$this->isValidCron($cronExpression)) {
            return false;
        }

        $nextRun = $this->getNextCronRunTime($cronExpression);

        DB::table('server_auto_restarts')->updateOrInsert(
            ['server_id' => $server->id],
            [
                'cron_expression' => $cronExpression,
                'enabled' => $enabled,
                'next_run_at' => $nextRun,
                'last_run_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        return true;
    }

    /**
     * Disables auto-restart for a server.
     */
    public function disableAutoRestart(Server $server): bool
    {
        return (bool) DB::table('server_auto_restarts')
            ->where('server_id', $server->id)
            ->update([
                'enabled' => false,
                'updated_at' => Carbon::now(),
            ]);
    }

    /**
     * Returns the current auto-restart status for a server.
     */
    public function getAutoRestartStatus(Server $server): array
    {
        $record = DB::table('server_auto_restarts')
            ->where('server_id', $server->id)
            ->first();

        if (!$record) {
            return [
                'enabled' => false,
                'cron_expression' => null,
                'next_run_at' => null,
                'last_run_at' => null,
            ];
        }

        return [
            'enabled' => (bool) $record->enabled,
            'cron_expression' => $record->cron_expression,
            'next_run_at' => $record->next_run_at,
            'last_run_at' => $record->last_run_at,
        ];
    }

    /**
     * Processes all pending scheduled restarts and returns the count of restarts performed.
     */
    public function processScheduledRestarts(): int
    {
        $now = Carbon::now();
        $count = 0;

        $pending = DB::table('server_auto_restarts')
            ->where('enabled', true)
            ->where('next_run_at', '<=', $now)
            ->get();

        foreach ($pending as $record) {
            $server = Server::find($record->server_id);

            if (!$server || $server->status === Server::STATUS_INSTALLING || $server->isSuspended()) {
                $this->updateNextRun($record);
                continue;
            }

            try {
                $this->daemonServerRepository->setServer($server)->power('restart');

                DB::table('server_auto_restarts')
                    ->where('id', $record->id)
                    ->update([
                        'last_run_at' => $now,
                        'updated_at' => $now,
                    ]);

                $this->updateNextRun($record);

                $count++;
            } catch (\Exception $e) {
                $this->updateNextRun($record);
            }
        }

        return $count;
    }

    /**
     * Updates the next run time for a given schedule record.
     */
    private function updateNextRun(object $record): void
    {
        $nextRun = $this->getNextCronRunTime($record->cron_expression);

        DB::table('server_auto_restarts')
            ->where('id', $record->id)
            ->update([
                'next_run_at' => $nextRun,
                'updated_at' => Carbon::now(),
            ]);
    }

    /**
     * Validates a cron expression string.
     */
    private function isValidCron(string $cron): bool
    {
        $parts = explode(' ', $cron);

        if (count($parts) !== 5) {
            return false;
        }

        $patterns = [
            '/^\*\/\d+$/',          // */n
            '/^\d+$/',              // number
            '/^\d+-\d+$/',         // range
            '/^\d+(,\d+)+$/',      // list
            '/^\*$/',               // wildcard
        ];

        foreach ($parts as $part) {
            $valid = false;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $part)) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculates the next run time for a cron expression.
     */
    private function getNextCronRunTime(string $cron): Carbon
    {
        $parts = explode(' ', $cron);
        $now = Carbon::now();

        $minute = $this->parseCronPart($parts[0], 0, 59, $now->minute);
        $hour = $this->parseCronPart($parts[1], 0, 23, $now->hour);
        $day = $this->parseCronPart($parts[2], 1, 31, $now->day);
        $month = $this->parseCronPart($parts[3], 1, 12, $now->month);
        $dayOfWeek = $this->parseCronPart($parts[4], 0, 6, $now->dayOfWeek);

        $next = Carbon::now();
        $next->minute = $minute;
        $next->hour = $hour;
        $next->day = $day;
        $next->month = $month;

        if ($next->lte($now)) {
            $next->addDay();
        }

        return $next;
    }

    /**
     * Parses a single cron field and returns the matching value.
     */
    private function parseCronPart(string $field, int $min, int $max, int $current): int
    {
        if ($field === '*') {
            return $current;
        }

        if (str_starts_with($field, '*/')) {
            $step = (int) substr($field, 2);
            return (int) ceil($current / $step) * $step;
        }

        if (str_contains($field, ',')) {
            $values = array_map('intval', explode(',', $field));
            foreach ($values as $value) {
                if ($value >= $current) {
                    return $value;
                }
            }
            return $values[0];
        }

        if (str_contains($field, '-')) {
            [$start, $end] = explode('-', $field);
            $start = (int) $start;
            $end = (int) $end;
            if ($current >= $start && $current <= $end) {
                return $current;
            }
            return $start;
        }

        return (int) $field;
    }
}
