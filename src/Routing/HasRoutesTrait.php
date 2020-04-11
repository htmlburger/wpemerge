<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

/**
 * Allow objects to have routes
 */
trait HasRoutesTrait {
	/**
	 * Array of registered routes
	 *
	 * @var RouteInterface[]
	 */
	protected $routes = [];

	/**
	 * Get routes.
	 *
	 * @return RouteInterface[]
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * Set routes.
	 *
	 * @param  RouteInterface[] $routes
	 * @return void
	 */
	public function setRoutes( $routes ) {
		$this->routes = $routes;
	}

	/**
	 * Add a route.
	 *
	 * @param  RouteInterface $route
	 * @return void
	 */
	public function addRoute( RouteInterface $route ) {
		$this->routes[] = $route;
	}
}
