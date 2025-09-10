<?php

namespace BlinkerBoy\Report\Facade;

use BlinkerBoy\Report\Reporter;
use Illuminate\Support\Facades\Facade;

/** @mixin Reporter */
class ReportType extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'report-type';
    }
}
