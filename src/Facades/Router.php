<?php

namespace CarbonFramework\Facades;

/**
 * Provide access to router service
 */
class Router extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.routing.router';
    }
}
