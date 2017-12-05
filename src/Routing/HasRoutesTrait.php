<?php

namespace Obsidian\Routing;

use Closure;
use Obsidian\Controllers\WordPress;

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
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $target, $handler = null ) {
		if ( $handler === null ) {
			$handler = WordPress::class . '@handle';
		}

		$route = new Route( $methods, $target, $handler );
		return $this->addRoute( $route );
	}

	/**
	 * Create and add a route group
	 *
	 * @param  string         $target
	 * @param  Closure        $closure
	 * @return RouteInterface
	 */
	public function group( $target, Closure $closure ) {
		$routeGroup = new RouteGroup( $target, $closure );
		return $this->addRoute( $routeGroup );
	}

	/**
	 * Create and add a route for the GET and HEAD methods
	 *
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function get( $target, $handler = null ) {
		return $this->route( ['GET', 'HEAD'], $target, $handler );
	}

	/**
	 * Create and add a route for the POST method
	 *
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function post( $target, $handler = null ) {
		return $this->route( ['POST'], $target, $handler );
	}

	/**
	 * Create and add a route for the PUT method
	 *
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function put( $target, $handler = null ) {
		return $this->route( ['PUT'], $target, $handler );
	}

	/**
	 * Create and add a route for the PATCH method
	 *
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function patch( $target, $handler = null ) {
		return $this->route( ['PATCH'], $target, $handler );
	}

	/**
	 * Create and add a route for the DELETE method
	 *
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function delete( $target, $handler = null ) {
		return $this->route( ['DELETE'], $target, $handler );
	}

	/**
	 * Create and add a route for the OPTIONS method
	 *
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function options( $target, $handler = null ) {
		return $this->route( ['OPTIONS'], $target, $handler );
	}

	/**
	 * Create and add a route for all supported methods
	 *
	 * @param  mixed               $target
	 * @param  string|Closure|null $handler
	 * @return RouteInterface
	 */
	public function any( $target, $handler = null ) {
		return $this->route( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $target, $handler );
	}
}
