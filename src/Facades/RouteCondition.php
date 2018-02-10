<?php

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the route condition factory.
 *
 * @codeCoverageIgnore
 */
class RouteCondition extends Facade {
	protected static function getFacadeAccessor() {
		return WPEMERGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY;
	}
}
