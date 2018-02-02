<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to session flashing service
 *
 * @codeCoverageIgnore
 */
class Flash extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_FLASH_KEY;
	}
}
