<?php

namespace CarbonFramework\Routing\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;

interface HasMiddlewareInterface {
	public function getMiddleware();

	public function addMiddleware( $middleware );

	public function add( $middleware );

	public function executeMiddleware( $middleware, $request, Closure $next );
}
