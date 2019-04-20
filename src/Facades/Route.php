<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Facades;

use WPEmerge\Support\Facade;

/**
 * Provide access to the route registrar.
 *
 * @codeCoverageIgnore
 * @see \WPEmerge\Routing\RouteBlueprint
 */
class Route extends Facade {
	protected static function getFacadeAccessor() {
		return static::getFacadeApplication()[ WPEMERGE_ROUTING_ROUTE_BLUEPRINT_KEY ];
	}
}
