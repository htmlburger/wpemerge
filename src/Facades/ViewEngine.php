<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the view service
 *
 * @codeCoverageIgnore
 */
class ViewEngine extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_VIEW_ENGINE_KEY;
	}
}
