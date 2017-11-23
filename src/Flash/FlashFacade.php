<?php

namespace Obsidian\Flash;

use Obsidian\Support\Facade;

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
