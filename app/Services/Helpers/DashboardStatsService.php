<?php

namespace Hexactyl\Services\Helpers;

use Hexactyl\Models\Server;
use Hexactyl\Models\User;
use Hexactyl\Models\Node;
use Hexactyl\Models\Allocation;


class DashboardStatsService
{
    /**
     * Get server statistics.
     */
    public function getServerStats(): array
    {
        $total = Server::count();
        $running = Server::where('status', 'running')->count();
        $stopped = Server::where('status', 'stopped')->count();
        $installing = Server::where('status', 'installing')->count();

        return [
            'total' => $total,
            'running' => $running,
            'stopped' => $stopped,
            'installing' => $installing,
            'suspended' => Server::where('suspended', true)->count(),
        ];
    }

    /**
     * Get user statistics.
     */
    public function getUserStats(): array
    {
        $total = User::count();
        $active = User::where('active', true)->count();
        $admin = User::where('root_admin', true)->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'admin' => $admin,
            'regular' => $total - $admin,
        ];
    }

    /**
     * Get node statistics.
     */
    public function getNodeStats(): array
    {
        $total = Node::count();
        $online = Node::where('maintenance_mode', false)->count();
        $offline = Node::where('maintenance_mode', true)->count();

        $totalMemory = Node::sum('memory');
        $totalDisk = Node::sum('disk');
        $usedMemory = Node::sum('memory') * (1 + (Node::avg('memory_overallocate') / 100));
        $usedDisk = Node::sum('disk') * (1 + (Node::avg('disk_overallocate') / 100));

        return [
            'total' => $total,
            'online' => $online,
            'offline' => $offline,
            'total_memory' => $totalMemory,
            'total_disk' => $totalDisk,
            'used_memory' => $usedMemory,
            'used_disk' => $usedDisk,
        ];
    }

    /**
     * Get overall panel statistics.
     */
    public function getOverallStats(): array
    {
        return [
            'servers' => $this->getServerStats(),
            'users' => $this->getUserStats(),
            'nodes' => $this->getNodeStats(),
            'allocations' => [
                'total' => Allocation::count(),
                'used' => Allocation::where('assigned', true)->count(),
            ],
            'eggs' => \Hexactyl\Models\Egg::count(),
            'nests' => \Hexactyl\Models\Nest::count(),
            'locations' => \Hexactyl\Models\Location::count(),
        ];
    }
}