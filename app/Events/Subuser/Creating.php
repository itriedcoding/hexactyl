<?php

namespace Hexactyl\\Events\Subuser;

use Hexactyl\\Events\Event;
use Hexactyl\\Models\Subuser;
use Illuminate\Queue\SerializesModels;

class Creating extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Subuser $subuser)
    {
    }
}
