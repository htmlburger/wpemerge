<?php

namespace WPEmerge\Routing;

use WPEmerge\Support\Facade;

/**
 * Provide access to router service
 *
 * @codeCoverageIgnore
 */
class RouterFacade extends Facade {
    protected static function getFacadeAccessor() {
        return WP_EMERGE_ROUTING_ROUTER_KEY;
    }
}
