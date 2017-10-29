<?php

namespace CarbonFramework\Flash;

use CarbonFramework\Support\Facade;

/**
 * Provide access to session flashing service
 *
 * @codeCoverageIgnore
 */
class FlashFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.flash.flash';
    }
}
