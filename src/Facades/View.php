<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the view service
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\View\ViewService
 */
class View extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_VIEW_SERVICE_KEY;
	}
}
