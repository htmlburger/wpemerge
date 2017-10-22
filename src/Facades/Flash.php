<?php

namespace CarbonFramework\Facades;

/**
 * Provide access to session flashing service
 */
class Flash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.flash.flash';
    }
}
