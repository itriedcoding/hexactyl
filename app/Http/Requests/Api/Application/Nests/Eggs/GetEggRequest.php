<?php

namespace Hexactyl\\Http\Requests\Api\Application\Nests\Eggs;

use Hexactyl\\Services\Acl\Api\AdminAcl;
use Hexactyl\\Http\Requests\Api\Application\ApplicationApiRequest;

class GetEggRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_EGGS;

    protected int $permission = AdminAcl::READ;
}
