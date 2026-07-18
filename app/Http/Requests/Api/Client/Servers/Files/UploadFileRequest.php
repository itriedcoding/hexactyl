<?php

namespace Hexactyl\\Http\Requests\Api\Client\Servers\Files;

use Hexactyl\\Models\Permission;
use Hexactyl\\Http\Requests\Api\Client\ClientApiRequest;

class UploadFileRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_FILE_CREATE;
    }
}
