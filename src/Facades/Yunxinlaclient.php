<?php

namespace Phpang\Yunxinlaclient\Facades;

use Illuminate\Support\Facades\Facade;

class Yunxinlaclient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Yunxinlaclient';
    }
    protected static function test()
    {
        return 'unxinlaClient';
    }
}
