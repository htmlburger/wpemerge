<?php

namespace CarbonFramework\Routing;

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
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $target, $handler );

	/**
	 * Create and add a route group
	 *
	 * @param  string         $target
	 * @param  Closure        $callable
	 * @return RouteInterface
	 */
	public function group( $target, Closure $callable );

	/**
	 * Create and add a route for the GET and HEAD methods
	 *
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function get( $target, $handler );

	/**
	 * Create and add a route for the POST method
	 *
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function post( $target, $handler );

	/**
	 * Create and add a route for the PUT method
	 *
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function put( $target, $handler );

	/**
	 * Create and add a route for the PATCH method
	 *
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function patch( $target, $handler );

	/**
	 * Create and add a route for the DELETE method
	 *
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function delete( $target, $handler );

	/**
	 * Create and add a route for the OPTIONS method
	 *
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function options( $target, $handler );

	/**
	 * Create and add a route for all supported methods
	 *
	 * @param  mixed          $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function any( $target, $handler );
}
