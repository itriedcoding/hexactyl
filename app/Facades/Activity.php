<?php

namespace Hexactyl\\Facades;

use Illuminate\Support\Facades\Facade;
use Hexactyl\\Services\Activity\ActivityLogService;

class Activity extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogService::class;
    }
}
