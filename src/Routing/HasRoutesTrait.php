<?php

namespace CarbonFramework\Routing;

use Closure;

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
	 * @param RouteInterface  $route
	 * @return RouteInterface
	 */
	public function addRoute( $route ) {
		$this->routes[] = $route;
		return $route;
	}

	/**
	 * Create and add a new route
	 * 
	 * @param  string[]       $methods
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $target, $handler ) {
		$route = new Route( $methods, $target, $handler );
		return $this->addRoute( $route );
	}

	/**
	 * Create and add a route group
	 * 
	 * @param  string         $target
	 * @param  Closure        $callable
	 * @return RouteInterface
	 */
	public function group( $target, Closure $callable ) {
		$routeGroup = new RouteGroup( $target, $callable );
		return $this->addRoute( $routeGroup );
	}

	/**
	 * Create and add a route for the GET and HEAD methods
	 * 
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function get( $target, $handler ) {
		return $this->route( ['GET', 'HEAD'], $target, $handler );
	}

	/**
	 * Create and add a route for the POST method
	 * 
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function post( $target, $handler ) {
		return $this->route( ['POST'], $target, $handler );
	}

	/**
	 * Create and add a route for the PUT method
	 * 
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function put( $target, $handler ) {
		return $this->route( ['PUT'], $target, $handler );
	}

	/**
	 * Create and add a route for the PATCH method
	 * 
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function patch( $target, $handler ) {
		return $this->route( ['PATCH'], $target, $handler );
	}

	/**
	 * Create and add a route for the DELETE method
	 * 
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function delete( $target, $handler ) {
		return $this->route( ['DELETE'], $target, $handler );
	}

	/**
	 * Create and add a route for the OPTIONS method
	 * 
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function options( $target, $handler ) {
		return $this->route( ['OPTIONS'], $target, $handler );
	}

	/**
	 * Create and add a route for all supported methods
	 * 
	 * @param  any            $target
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function any( $target, $handler ) {
		return $this->route( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $target, $handler );
	}
}
