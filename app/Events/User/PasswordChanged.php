<?php

namespace Hexactyl\\Events\User;

use Hexactyl\\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

final class PasswordChanged
{
    use Dispatchable;

    public function __construct(public readonly User $user)
    {
    }
}
