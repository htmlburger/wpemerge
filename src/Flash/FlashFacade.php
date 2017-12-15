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
		return WPEMERGE_FLASH_KEY;
	}
}
