<?php

namespace Hexactyl\\Http\Requests\Api\Client\Servers\Databases;

use Hexactyl\\Models\Permission;
use Hexactyl\\Contracts\Http\ClientPermissionsRequest;
use Hexactyl\\Http\Requests\Api\Client\ClientApiRequest;

class GetDatabasesRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    public function permission(): string
    {
        return Permission::ACTION_DATABASE_READ;
    }
}
