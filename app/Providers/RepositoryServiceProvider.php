<?php

namespace Hexactyl\\Providers;

use Illuminate\Support\ServiceProvider;
use Hexactyl\\Repositories\Eloquent\EggRepository;
use Hexactyl\\Repositories\Eloquent\NestRepository;
use Hexactyl\\Repositories\Eloquent\NodeRepository;
use Hexactyl\\Repositories\Eloquent\TaskRepository;
use Hexactyl\\Repositories\Eloquent\UserRepository;
use Hexactyl\\Repositories\Eloquent\ApiKeyRepository;
use Hexactyl\\Repositories\Eloquent\ServerRepository;
use Hexactyl\\Repositories\Eloquent\SessionRepository;
use Hexactyl\\Repositories\Eloquent\SubuserRepository;
use Hexactyl\\Repositories\Eloquent\DatabaseRepository;
use Hexactyl\\Repositories\Eloquent\LocationRepository;
use Hexactyl\\Repositories\Eloquent\ScheduleRepository;
use Hexactyl\\Repositories\Eloquent\SettingsRepository;
use Hexactyl\\Repositories\Eloquent\AllocationRepository;
use Hexactyl\\Contracts\Repository\EggRepositoryInterface;
use Hexactyl\\Repositories\Eloquent\EggVariableRepository;
use Hexactyl\\Contracts\Repository\NestRepositoryInterface;
use Hexactyl\\Contracts\Repository\NodeRepositoryInterface;
use Hexactyl\\Contracts\Repository\TaskRepositoryInterface;
use Hexactyl\\Contracts\Repository\UserRepositoryInterface;
use Hexactyl\\Repositories\Eloquent\DatabaseHostRepository;
use Hexactyl\\Contracts\Repository\ApiKeyRepositoryInterface;
use Hexactyl\\Contracts\Repository\ServerRepositoryInterface;
use Hexactyl\\Repositories\Eloquent\ServerVariableRepository;
use Hexactyl\\Contracts\Repository\SessionRepositoryInterface;
use Hexactyl\\Contracts\Repository\SubuserRepositoryInterface;
use Hexactyl\\Contracts\Repository\DatabaseRepositoryInterface;
use Hexactyl\\Contracts\Repository\LocationRepositoryInterface;
use Hexactyl\\Contracts\Repository\ScheduleRepositoryInterface;
use Hexactyl\\Contracts\Repository\SettingsRepositoryInterface;
use Hexactyl\\Contracts\Repository\AllocationRepositoryInterface;
use Hexactyl\\Contracts\Repository\EggVariableRepositoryInterface;
use Hexactyl\\Contracts\Repository\DatabaseHostRepositoryInterface;
use Hexactyl\\Contracts\Repository\ServerVariableRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register all the repository bindings.
     */
    public function register(): void
    {
        // Eloquent Repositories
        $this->app->bind(AllocationRepositoryInterface::class, AllocationRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(DatabaseRepositoryInterface::class, DatabaseRepository::class);
        $this->app->bind(DatabaseHostRepositoryInterface::class, DatabaseHostRepository::class);
        $this->app->bind(EggRepositoryInterface::class, EggRepository::class);
        $this->app->bind(EggVariableRepositoryInterface::class, EggVariableRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(NestRepositoryInterface::class, NestRepository::class);
        $this->app->bind(NodeRepositoryInterface::class, NodeRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(ServerRepositoryInterface::class, ServerRepository::class);
        $this->app->bind(ServerVariableRepositoryInterface::class, ServerVariableRepository::class);
        $this->app->bind(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
        $this->app->bind(SubuserRepositoryInterface::class, SubuserRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
