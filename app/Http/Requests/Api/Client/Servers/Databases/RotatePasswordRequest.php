<?php

namespace Hexactyl\\Http\Requests\Api\Client\Servers\Databases;

use Hexactyl\\Models\Permission;
use Hexactyl\\Http\Requests\Api\Client\ClientApiRequest;

class RotatePasswordRequest extends ClientApiRequest
{
    /**
     * Check that the user has permission to rotate the password.
     */
    public function permission(): string
    {
        return Permission::ACTION_DATABASE_UPDATE;
    }
}
