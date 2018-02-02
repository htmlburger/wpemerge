<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the framework instance
 *
 * @codeCoverageIgnore
 */
class Framework extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_FRAMEWORK_KEY;
	}
}
