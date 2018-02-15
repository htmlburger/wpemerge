<?php

namespace WPEmerge\Routing;

use Closure;
use WPEmerge\Controllers\WordPressController;

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
	 * Get registered routes
	 *
	 * @return RouteInterface[]
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * Add a route
	 *
	 * @param  RouteInterface $route
	 * @return RouteInterface
	 */
	public function addRoute( $route ) {
		$this->routes[] = $route;
		return $route;
	}

	/**
	 * Create and add a new route
	 *
	 * @param  string[]            $methods
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $condition, $handler = null ) {
		if ( $handler === null ) {
			$handler = WordPressController::class . '@handle';
		}

		$route = new Route( $methods, $condition, $handler );
		return $this->addRoute( $route );
	}

	/**
	 * Create and add a route group
	 *
	 * @param  string|Closure $condition
	 * @param  Closure|null   $routes
	 * @return RouteInterface
	 */
	public function group( $condition, $routes = null ) {
		$routeGroup = new RouteGroup( $condition, $routes );
		return $this->addRoute( $routeGroup );
	}

	/**
	 * Create and add a route for the GET and HEAD methods
	 *
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function get( $condition, $handler = null ) {
		return $this->route( ['GET', 'HEAD'], $condition, $handler );
	}

	/**
	 * Create and add a route for the POST method
	 *
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function post( $condition, $handler = null ) {
		return $this->route( ['POST'], $condition, $handler );
	}

	/**
	 * Create and add a route for the PUT method
	 *
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function put( $condition, $handler = null ) {
		return $this->route( ['PUT'], $condition, $handler );
	}

	/**
	 * Create and add a route for the PATCH method
	 *
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function patch( $condition, $handler = null ) {
		return $this->route( ['PATCH'], $condition, $handler );
	}

	/**
	 * Create and add a route for the DELETE method
	 *
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function delete( $condition, $handler = null ) {
		return $this->route( ['DELETE'], $condition, $handler );
	}

	/**
	 * Create and add a route for the OPTIONS method
	 *
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function options( $condition, $handler = null ) {
		return $this->route( ['OPTIONS'], $condition, $handler );
	}

	/**
	 * Create and add a route for all supported methods
	 *
	 * @param  mixed               $condition
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function any( $condition, $handler = null ) {
		return $this->route( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $condition, $handler );
	}
}
