<?php

namespace CarbonFramework\Routing;

use Closure;

trait HasRoutesTrait {
	protected $routes = [];

	public function getRoutes() {
		return $this->routes;
	}

	public function addRoute( $route ) {
		$this->routes[] = $route;
		return $route;
	}

	public function route( $methods, $target, $handler ) {
		$route = new Route( $methods, $target, $handler );
		return $this->addRoute( $route );
	}

	public function group( $target, Closure $callable ) {
		$routeGroup = new RouteGroup( $target, $callable );
		return $this->addRoute( $routeGroup );
	}

	public function get( $target, $handler ) {
		return $this->route( ['GET', 'HEAD'], $target, $handler );
	}

	public function post( $target, $handler ) {
		return $this->route( ['POST'], $target, $handler );
	}

	public function put( $target, $handler ) {
		return $this->route( ['PUT'], $target, $handler );
	}

	public function patch( $target, $handler ) {
		return $this->route( ['PATCH'], $target, $handler );
	}

	public function delete( $target, $handler ) {
		return $this->route( ['DELETE'], $target, $handler );
	}

	public function options( $target, $handler ) {
		return $this->route( ['OPTIONS'], $target, $handler );
	}

	public function any( $target, $handler ) {
		return $this->route( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $target, $handler );
	}
}
