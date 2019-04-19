<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

/**
 * Interface for HasRoutesTrait
 */
interface HasRoutesInterface {
	/**
	 * Get routes.
	 *
	 * @return array<RouteInterface>
	 */
	public function getRoutes();

	/**
	 * Set routes.
	 *
	 * @param  array<RouteInterface> $routes
	 * @return void
	 */
	public function setRoutes( $routes );

	/**
	 * Add a route.
	 *
	 * @param  RouteInterface $route
	 * @return void
	 */
	public function addRoute( RouteInterface $route );
}
