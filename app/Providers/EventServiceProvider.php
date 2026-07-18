<?php

namespace Hexactyl\\Providers;

use Hexactyl\\Models\User;
use Hexactyl\\Models\Server;
use Hexactyl\\Models\Subuser;
use Hexactyl\\Models\EggVariable;
use Hexactyl\\Observers\UserObserver;
use Hexactyl\\Observers\ServerObserver;
use Hexactyl\\Observers\SubuserObserver;
use Hexactyl\\Listeners\TwoFactorListener;
use Hexactyl\\Listeners\RevocationListener;
use Hexactyl\\Observers\EggVariableObserver;
use Hexactyl\\Listeners\AuthenticationListener;
use Hexactyl\\Events\Server\Installed as ServerInstalledEvent;
use Hexactyl\\Notifications\ServerInstalled as ServerInstalledNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        ServerInstalledEvent::class => [ServerInstalledNotification::class],
    ];

    protected $subscribe = [
        AuthenticationListener::class,
        RevocationListener::class,
        TwoFactorListener::class,
    ];

    protected static $shouldDiscoverEvents = false;

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        User::observe(UserObserver::class);
        Server::observe(ServerObserver::class);
        Subuser::observe(SubuserObserver::class);
        EggVariable::observe(EggVariableObserver::class);
    }
}
