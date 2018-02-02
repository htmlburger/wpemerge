<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the router service
 *
 * @codeCoverageIgnore
 */
class Router extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_ROUTING_ROUTER_KEY;
	}
}
