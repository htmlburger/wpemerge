<?php

namespace WPEmerge\Input;

use WPEmerge\Support\Facade;

/**
 * Provide access to old input service
 *
 * @codeCoverageIgnore
 */
class OldInputFacade extends Facade {
    protected static function getFacadeAccessor() {
        return WP_EMERGE_OLD_INPUT_KEY;
    }
}
