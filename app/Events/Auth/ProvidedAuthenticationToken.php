<?php

namespace Hexactyl\\Events\Auth;

use Hexactyl\\Models\User;
use Hexactyl\\Events\Event;

class ProvidedAuthenticationToken extends Event
{
    public function __construct(public User $user, public bool $recovery = false)
    {
    }
}
