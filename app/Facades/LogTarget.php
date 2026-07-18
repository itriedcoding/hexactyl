<?php

namespace Hexactyl\\Facades;

use Illuminate\Support\Facades\Facade;
use Hexactyl\\Services\Activity\ActivityLogTargetableService;

/**
 * @mixin \Hexactyl\\Services\Activity\ActivityLogTargetableService
 */
class LogTarget extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogTargetableService::class;
    }
}
