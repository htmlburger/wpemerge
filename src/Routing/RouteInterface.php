<?php

namespace CarbonFramework\Routing;

use Psr\Http\Message\RequestInterface;

interface RouteInterface {
	public function satisfied();

	public function handle( RequestInterface $request );
}
