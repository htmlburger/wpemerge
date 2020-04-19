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
 * Interface for HasRoutesTrait
 */
interface HasRoutesInterface {
	/**
	 * Get routes.
	 *
	 * @return RouteInterface[]
	 */
	public function getRoutes();

	/**
	 * Add a route.
	 *
	 * @param  RouteInterface $route
	 * @return void
	 */
	public function addRoute( RouteInterface $route );

	/**
	 * Remove a route.
	 *
	 * @param  RouteInterface $route
	 * @return void
	 */
	public function removeRoute( RouteInterface $route );
}
