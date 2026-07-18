<?php

namespace Hexactyl\\Http\Requests\Api\Application\Users;

use Hexactyl\\Services\Acl\Api\AdminAcl;
use Hexactyl\\Http\Requests\Api\Application\ApplicationApiRequest;

class DeleteUserRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_USERS;

    protected int $permission = AdminAcl::WRITE;
}
