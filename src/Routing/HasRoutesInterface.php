<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Helpers\Handler;

/**
 * Interface for HasRoutesTrait
 */
interface HasRoutesInterface {
	/**
	 * Get registered routes
	 *
	 * @return array<RouteInterface>
	 */
	public function getRoutes();

	/**
	 * Add a route
	 *
	 * @param  RouteInterface $route
	 * @return RouteInterface
	 */
	public function addRoute( $route );

	/**
	 * Create and add a new route
	 *
	 * @param  array<string>  $methods
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $condition, Handler $handler );

	/**
	 * Create and add a route for the GET and HEAD methods
	 *
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function get( $condition, Handler $handler );

	/**
	 * Create and add a route for the POST method
	 *
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function post( $condition, Handler $handler );

	/**
	 * Create and add a route for the PUT method
	 *
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function put( $condition, Handler $handler );

	/**
	 * Create and add a route for the PATCH method
	 *
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function patch( $condition, Handler $handler );

	/**
	 * Create and add a route for the DELETE method
	 *
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function delete( $condition, Handler $handler );

	/**
	 * Create and add a route for the OPTIONS method
	 *
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function options( $condition, Handler $handler );

	/**
	 * Create and add a route for all supported methods
	 *
	 * @param  mixed          $condition
	 * @param  Handler        $handler
	 * @return RouteInterface
	 */
	public function any( $condition, Handler $handler );
}
