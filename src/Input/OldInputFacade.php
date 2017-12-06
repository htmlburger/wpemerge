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
        return 'framework.old_input.old_input';
    }
}
