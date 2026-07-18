<?php

namespace Hexactyl\Services\Servers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Hexactyl\Models\Server;
use Illuminate\Support\Facades\Auth;

class ServerSnapshotService
{
    /**
     * Creates a configuration snapshot of a server.
     */
    public function createSnapshot(Server $server, string $name = null): array
    {
        $server->load(['egg', 'node', 'allocations']);

        $snapshotData = [
            'server_id' => $server->id,
            'name' => $server->name,
            'description' => $server->description,
            'status' => $server->status,
            'skip_scripts' => $server->skip_scripts,
            'memory' => $server->memory,
            'swap' => $server->swap,
            'disk' => $server->disk,
            'io' => $server->io,
            'cpu' => $server->cpu,
            'threads' => $server->threads,
            'oom_disabled' => $server->oom_disabled,
            'allocation_id' => $server->allocation_id,
            'nest_id' => $server->nest_id,
            'egg_id' => $server->egg_id,
            'startup' => $server->startup,
            'image' => $server->image,
            'database_limit' => $server->database_limit,
            'allocation_limit' => $server->allocation_limit,
            'backup_limit' => $server->backup_limit,
            'allocations' => $server->allocations->pluck('id')->toArray(),
            'variables' => $server->variables->map(function ($var) {
                return [
                    'variable_id' => $var->variable_id ?? $var->id,
                    'name' => $var->name,
                    'value' => $var->server_value ?? $var->default_value,
                ];
            })->toArray(),
        ];

        $snapshotName = $name ?? 'Snapshot ' . Carbon::now()->format('Y-m-d H:i:s');

        $snapshot = DB::table('server_snapshots')->insertGetId([
            'server_id' => $server->id,
            'name' => $snapshotName,
            'description' => "Auto-generated snapshot for {$server->name}",
            'snapshot_data' => json_encode($snapshotData),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return [
            'id' => $snapshot,
            'name' => $snapshotName,
            'server_id' => $server->id,
            'created_at' => Carbon::now()->toISOString(),
        ];
    }

    /**
     * Lists all snapshots for a given server.
     */
    public function getSnapshots(Server $server): array
    {
        $snapshots = DB::table('server_snapshots')
            ->where('server_id', $server->id)
            ->orderByDesc('created_at')
            ->get();

        return $snapshots->map(function ($snapshot) {
            return [
                'id' => $snapshot->id,
                'name' => $snapshot->name,
                'description' => $snapshot->description,
                'created_by' => $snapshot->created_by,
                'created_at' => $snapshot->created_at,
            ];
        })->toArray();
    }

    /**
     * Restores a server from a given snapshot.
     */
    public function restoreSnapshot(Server $server, int $snapshotId): bool
    {
        $snapshot = DB::table('server_snapshots')
            ->where('id', $snapshotId)
            ->where('server_id', $server->id)
            ->first();

        if (!$snapshot) {
            return false;
        }

        $data = json_decode($snapshot->snapshot_data, true);

        if (!is_array($data)) {
            return false;
        }

        $server->update([
            'name' => $data['name'] ?? $server->name,
            'description' => $data['description'] ?? $server->description,
            'skip_scripts' => $data['skip_scripts'] ?? $server->skip_scripts,
            'memory' => $data['memory'] ?? $server->memory,
            'swap' => $data['swap'] ?? $server->swap,
            'disk' => $data['disk'] ?? $server->disk,
            'io' => $data['io'] ?? $server->io,
            'cpu' => $data['cpu'] ?? $server->cpu,
            'threads' => $data['threads'] ?? $server->threads,
            'oom_disabled' => $data['oom_disabled'] ?? $server->oom_disabled,
            'nest_id' => $data['nest_id'] ?? $server->nest_id,
            'egg_id' => $data['egg_id'] ?? $server->egg_id,
            'startup' => $data['startup'] ?? $server->startup,
            'image' => $data['image'] ?? $server->image,
            'database_limit' => $data['database_limit'] ?? $server->database_limit,
            'allocation_limit' => $data['allocation_limit'] ?? $server->allocation_limit,
            'backup_limit' => $data['backup_limit'] ?? $server->backup_limit,
        ]);

        return true;
    }

    /**
     * Deletes a snapshot by its ID.
     */
    public function deleteSnapshot(int $snapshotId): bool
    {
        return (bool) DB::table('server_snapshots')
            ->where('id', $snapshotId)
            ->delete();
    }
}
