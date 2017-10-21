<?php

namespace CarbonFramework\Routing;

use Closure;

trait HasRoutesTrait {
	protected $routes = [];

	public function getRoutes() {
		return $this->routes;
	}

	public function addRoute( $methods, $target, $handler ) {
		$route = new Route( $methods, $target, $handler );
		$this->routes[] = $route;
		return $route;
	}

	public function group( $target, Closure $callable ) {
		$routeGroup = new RouteGroup( $target, $callable );
		$this->routes[] = $routeGroup;
		return $routeGroup;
	}

	public function get( $target, $handler ) {
		return $this->addRoute( ['GET', 'HEAD'], $target, $handler );
	}

	public function post( $target, $handler ) {
		return $this->addRoute( ['POST'], $target, $handler );
	}

	public function put( $target, $handler ) {
		return $this->addRoute( ['PUT'], $target, $handler );
	}

	public function patch( $target, $handler ) {
		return $this->addRoute( ['PATCH'], $target, $handler );
	}

	public function delete( $target, $handler ) {
		return $this->addRoute( ['DELETE'], $target, $handler );
	}

	public function options( $target, $handler ) {
		return $this->addRoute( ['OPTIONS'], $target, $handler );
	}

	public function any( $target, $handler ) {
		return $this->addRoute( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $target, $handler );
	}
}
