<?php

namespace Hexactyl\\Http\Requests\Api\Application\Servers\Databases;

use Hexactyl\\Services\Acl\Api\AdminAcl;
use Hexactyl\\Http\Requests\Api\Application\ApplicationApiRequest;

class GetServerDatabaseRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_SERVER_DATABASES;

    protected int $permission = AdminAcl::READ;
}
