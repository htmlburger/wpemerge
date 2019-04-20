<?php /** @noinspection PhpLanguageLevelInspection */
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
 *
 * @method static \WPEmerge\Routing\RouteBlueprint attributes( array $attributes )
 * @method static \WPEmerge\Routing\RouteBlueprint methods( array $methods )
 * @method static \WPEmerge\Routing\RouteBlueprint url( string $url, array $where = [] )
 * @method static \WPEmerge\Routing\RouteBlueprint where( string|array|\Closure|\WPEmerge\Routing\Conditions\ConditionInterface $condition, ...$arguments )
 * @method static \WPEmerge\Routing\RouteBlueprint middleware( string|array $middleware)
 * @method static \WPEmerge\Routing\RouteBlueprint setNamespace( string $namespace )
 * @method static void group( string|\Closure $routes )
 * @method static void handle( string|\Closure $routes )
 * @method static \WPEmerge\Routing\RouteBlueprint get()
 * @method static \WPEmerge\Routing\RouteBlueprint post()
 * @method static \WPEmerge\Routing\RouteBlueprint put()
 * @method static \WPEmerge\Routing\RouteBlueprint patch()
 * @method static \WPEmerge\Routing\RouteBlueprint delete()
 * @method static \WPEmerge\Routing\RouteBlueprint options()
 * @method static \WPEmerge\Routing\RouteBlueprint any()
 * @method static void all()
 */
class Route extends Facade {
	protected static function getFacadeAccessor() {
		return static::getFacadeApplication()[ WPEMERGE_ROUTING_ROUTE_BLUEPRINT_KEY ];
	}
}
