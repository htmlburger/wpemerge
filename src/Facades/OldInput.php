<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to old input service
 *
 * @codeCoverageIgnore
 */
class OldInput extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_OLD_INPUT_KEY;
	}
}
