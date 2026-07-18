<?php

namespace Hexactyl\\Http\Requests\Api\Client\Servers\Network;

use Hexactyl\\Models\Permission;
use Hexactyl\\Http\Requests\Api\Client\ClientApiRequest;

class DeleteAllocationRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_ALLOCATION_DELETE;
    }
}
