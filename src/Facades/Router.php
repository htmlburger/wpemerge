<?php

namespace CarbonFramework\Facades;

class Router extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.router';
    }
}
