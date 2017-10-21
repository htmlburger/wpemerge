<?php

namespace CarbonFramework\Routing;

use Closure;

trait HasRoutesTrait {
	protected $routes = [];

	public function getRoutes() {
		return $this->routes;
	}

	public function addRoute( $methods, $target, $handler ) {
		$this->routes[] = new Route( $methods, $target, $handler );
	}

	public function group( $target, Closure $callable ) {
		$this->routes[] = new RouteGroup( $target, $callable );
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
