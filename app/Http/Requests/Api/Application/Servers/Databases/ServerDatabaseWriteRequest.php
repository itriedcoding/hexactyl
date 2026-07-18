<?php

namespace Hexactyl\\Http\Requests\Api\Application\Servers\Databases;

use Hexactyl\\Services\Acl\Api\AdminAcl;

class ServerDatabaseWriteRequest extends GetServerDatabasesRequest
{
    protected int $permission = AdminAcl::WRITE;
}
