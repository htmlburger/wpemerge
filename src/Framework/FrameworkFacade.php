<?php

namespace Obsidian\Framework;

use Obsidian\Support\Facade;

/**
 * Provide access to the framework instance
 *
 * @codeCoverageIgnore
 */
class FrameworkFacade extends Facade {
    protected static function getFacadeAccessor() {
        return OBSIDIAN_FRAMEWORK_KEY;
    }
}
