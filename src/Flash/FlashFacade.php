<?php

namespace WPEmerge\Flash;

use WPEmerge\Support\Facade;

/**
 * Provide access to session flashing service
 *
 * @codeCoverageIgnore
 */
class FlashFacade extends Facade {
    protected static function getFacadeAccessor() {
        return WP_EMERGE_FLASH_KEY;
    }
}
