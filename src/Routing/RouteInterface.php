<?php

namespace CarbonFramework\Routing;

use Psr\Http\Message\RequestInterface;

interface RouteInterface extends HasMiddlewareInterface {
	public function satisfied();

	public function handle( RequestInterface $request );
}
