<?php

namespace Hexactyl\\Facades;

use Illuminate\Support\Facades\Facade;
use Hexactyl\\Services\Activity\ActivityLogBatchService;

class LogBatch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogBatchService::class;
    }
}
