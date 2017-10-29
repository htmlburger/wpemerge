<?php

namespace CarbonFramework\Routing;

use CarbonFramework\Support\Facade;

/**
 * Provide access to router service
 *
 * @codeCoverageIgnore
 */
class RouterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.routing.router';
    }
}
