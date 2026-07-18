<?php

namespace Hexactyl\\Http\Requests\Api\Application\Nodes;

use Hexactyl\\Services\Acl\Api\AdminAcl;

class GetNodeConfigurationRequest extends GetNodesRequest
{
    protected int $permission = AdminAcl::WRITE;
}
