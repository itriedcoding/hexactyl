<?php

namespace Hexactyl\\Http\Requests\Api\Client\Servers\Files;

use Hexactyl\\Models\Permission;
use Hexactyl\\Contracts\Http\ClientPermissionsRequest;
use Hexactyl\\Http\Requests\Api\Client\ClientApiRequest;

class CopyFileRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    public function permission(): string
    {
        return Permission::ACTION_FILE_CREATE;
    }

    public function rules(): array
    {
        return [
            'location' => 'required|string',
        ];
    }
}
