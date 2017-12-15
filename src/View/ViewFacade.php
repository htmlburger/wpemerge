<?php

namespace WPEmerge\View;

use WPEmerge\Support\Facade;

/**
 * Provide access to the view service
 *
 * @codeCoverageIgnore
 */
class ViewFacade extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_VIEW_KEY;
	}
}
