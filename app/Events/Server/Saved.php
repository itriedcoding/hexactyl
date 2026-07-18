<?php

namespace Hexactyl\\Events\Server;

use Hexactyl\\Events\Event;
use Hexactyl\\Models\Server;
use Illuminate\Queue\SerializesModels;

class Saved extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Server $server)
    {
    }
}
