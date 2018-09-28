<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the view service
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\View\ViewEngineInterface
 * @see \WPEmerge\View\PhpViewEngine (Default)
 */
class ViewEngine extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_VIEW_ENGINE_KEY;
	}
}
