<?php

namespace Hexactyl\\Http\Requests\Api\Application\Servers;

use Hexactyl\\Services\Acl\Api\AdminAcl;
use Hexactyl\\Http\Requests\Api\Application\ApplicationApiRequest;

class GetExternalServerRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_SERVERS;

    protected int $permission = AdminAcl::READ;
}
