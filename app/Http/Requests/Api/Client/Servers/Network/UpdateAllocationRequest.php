<?php

namespace Hexactyl\\Http\Requests\Api\Client\Servers\Network;

use Hexactyl\\Models\Allocation;
use Hexactyl\\Models\Permission;
use Hexactyl\\Http\Requests\Api\Client\ClientApiRequest;

class UpdateAllocationRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_ALLOCATION_UPDATE;
    }

    public function rules(): array
    {
        $rules = Allocation::getRules();

        return [
            'notes' => array_merge($rules['notes'], ['present']),
        ];
    }
}
