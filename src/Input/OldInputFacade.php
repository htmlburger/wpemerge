<?php

namespace Obsidian\Input;

use Obsidian\Support\Facade;

/**
 * Provide access to old input service
 *
 * @codeCoverageIgnore
 */
class OldInputFacade extends Facade {
    protected static function getFacadeAccessor() {
        return OBSIDIAN_OLD_INPUT_KEY;
    }
}
