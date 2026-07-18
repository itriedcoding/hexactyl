<?php

namespace Hexactyl\\Http\Requests\Api\Application\Users;

use Hexactyl\\Services\Acl\Api\AdminAcl as Acl;
use Hexactyl\\Http\Requests\Api\Application\ApplicationApiRequest;

class GetUsersRequest extends ApplicationApiRequest
{
    protected ?string $resource = Acl::RESOURCE_USERS;

    protected int $permission = Acl::READ;
}
