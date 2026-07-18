<?php

namespace Hexactyl\Services\Servers;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Arr;
use Hexactyl\Models\Server;
use Hexactyl\Models\Allocation;
use Illuminate\Database\ConnectionInterface;
use Hexactyl\Repositories\Eloquent\ServerRepository;
use Hexactyl\Repositories\Wings\DaemonServerRepository;
use Hexactyl\Repositories\Eloquent\ServerVariableRepository;

class ServerCloneService
{
    public function __construct(
        private ConnectionInterface $connection,
        private ServerRepository $repository,
        private DaemonServerRepository $daemonServerRepository,
        private ServerVariableRepository $serverVariableRepository,
    ) {
    }

    /**
     * Creates a clone of a server with the same configuration.
     *
     * @throws \Throwable
     */
    public function handle(Server $server, array $options = []): Server
    {
        $cloneName = Arr::get($options, 'name', $server->name . ' (Clone)');
        $ownerId = Arr::get($options, 'owner_id', $server->owner_id);

        $uuid = $this->generateUniqueUuidCombo();

        /** @var Server $clone */
        $clone = $this->connection->transaction(function () use ($server, $cloneName, $ownerId, $uuid) {
            $clone = $this->repository->create([
                'uuid' => $uuid,
                'uuidShort' => substr($uuid, 0, 8),
                'node_id' => $server->node_id,
                'name' => $cloneName,
                'description' => $server->description,
                'status' => Server::STATUS_INSTALLING,
                'skip_scripts' => $server->skip_scripts,
                'owner_id' => $ownerId,
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
            ]);

            $this->cloneAllocations($server, $clone);
            $this->cloneEggVariables($server, $clone);

            return $clone;
        }, 5);

        try {
            $this->daemonServerRepository->setServer($clone)->create(false);
        } catch (\Exception $exception) {
            $clone->delete();

            throw $exception;
        }

        return $clone;
    }

    /**
     * Returns available clone options for a given server.
     */
    public function getCloneOptions(Server $server): array
    {
        return [
            'name' => $server->name . ' (Clone)',
            'owner_id' => $server->owner_id,
            'node_id' => $server->node_id,
            'egg_id' => $server->egg_id,
            'nest_id' => $server->nest_id,
            'image' => $server->image,
            'startup' => $server->startup,
            'memory' => $server->memory,
            'swap' => $server->swap,
            'disk' => $server->disk,
            'io' => $server->io,
            'cpu' => $server->cpu,
            'threads' => $server->threads,
            'oom_disabled' => $server->oom_disabled,
            'allocation_id' => $server->allocation_id,
            'database_limit' => $server->database_limit,
            'allocation_limit' => $server->allocation_limit,
            'backup_limit' => $server->backup_limit,
            'allocations' => $server->allocations->pluck('id')->toArray(),
        ];
    }

    /**
     * Validates whether a server can be cloned.
     */
    public function validateClone(Server $server): bool
    {
        if ($server->status === Server::STATUS_INSTALLING) {
            return false;
        }

        if ($server->status === Server::STATUS_SUSPENDED) {
            return false;
        }

        if (!is_null($server->transfer)) {
            return false;
        }

        if (!$server->node) {
            return false;
        }

        if ($server->node->isUnderMaintenance()) {
            return false;
        }

        return true;
    }

    /**
     * Copy allocations from the source server to the clone.
     */
    private function cloneAllocations(Server $server, Server $clone): void
    {
        $additionalAllocations = $server->allocations()
            ->where('id', '!=', $server->allocation_id)
            ->get();

        foreach ($additionalAllocations as $allocation) {
            Allocation::query()->where('id', $allocation->id)->update([
                'server_id' => $clone->id,
            ]);
        }
    }

    /**
     * Copy egg variables from the source server to the clone.
     */
    private function cloneEggVariables(Server $server, Server $clone): void
    {
        $variables = $server->variables()
            ->wherePivot('server_id', $server->id)
            ->get();

        if ($variables->isEmpty()) {
            return;
        }

        $records = $variables->map(function ($variable) use ($clone) {
            return [
                'server_id' => $clone->id,
                'variable_id' => $variable->variable_id,
                'variable_value' => $variable->pivot->variable_value,
            ];
        })->toArray();

        $this->serverVariableRepository->insert($records);
    }

    /**
     * Create a unique UUID and UUID-Short combo for a server.
     */
    private function generateUniqueUuidCombo(): string
    {
        $uuid = Uuid::uuid4()->toString();

        if (!$this->repository->isUniqueUuidCombo($uuid, substr($uuid, 0, 8))) {
            return $this->generateUniqueUuidCombo();
        }

        return $uuid;
    }
}
