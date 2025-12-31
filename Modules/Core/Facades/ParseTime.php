<?php

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Core\Facades\Services\ParseTimeService;

/**
 * @method static void setTimeZone(?string $timeZone = null)
 * @method static string parse(string $time)
 */
class ParseTime extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ParseTimeService::class;
    }
}