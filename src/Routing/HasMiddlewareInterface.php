<?php

namespace CarbonFramework\Routing;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HasMiddlewareInterface {
	public function getMiddleware();

	public function addMiddleware( $middleware );

	public function add( $middleware );

	public function executeMiddleware( RequestInterface $request, $next );
}
