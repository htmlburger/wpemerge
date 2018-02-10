<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the responses service.
 *
 * @codeCoverageIgnore
 */
class Response extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_RESPONSE_SERVICE_KEY;
	}
}
