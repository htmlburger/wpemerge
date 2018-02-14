<?php

namespace WPEmerge\Routing;

use Closure;

/**
 * Interface for HasRoutesTrait
 */
interface HasRoutesInterface {
	/**
	 * Get registered routes
	 *
	 * @return RouteInterface[]
	 */
	public function getRoutes();

	/**
	 * Add a route
	 *
	 * @param RouteInterface  $route
	 * @return RouteInterface
	 */
	public function addRoute( $route );

	/**
	 * Create and add a new route
	 *
	 * @param  string[]       $methods
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $condition, $handler );

	/**
	 * Create and add a route group
	 *
	 * @param  string         $condition
	 * @param  Closure        $closure
	 * @return RouteInterface
	 */
	public function group( $condition, Closure $closure );

	/**
	 * Create and add a route for the GET and HEAD methods
	 *
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function get( $condition, $handler );

	/**
	 * Create and add a route for the POST method
	 *
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function post( $condition, $handler );

	/**
	 * Create and add a route for the PUT method
	 *
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function put( $condition, $handler );

	/**
	 * Create and add a route for the PATCH method
	 *
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function patch( $condition, $handler );

	/**
	 * Create and add a route for the DELETE method
	 *
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function delete( $condition, $handler );

	/**
	 * Create and add a route for the OPTIONS method
	 *
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function options( $condition, $handler );

	/**
	 * Create and add a route for all supported methods
	 *
	 * @param  mixed          $condition
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function any( $condition, $handler );
}
