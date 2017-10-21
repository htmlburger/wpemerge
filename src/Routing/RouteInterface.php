<?php

namespace CarbonFramework\Routing;

use CarbonFramework\Routing\Middleware\HasMiddlewareInterface;

interface RouteInterface extends HasMiddlewareInterface {
	public function satisfied();

	public function handle( $request );
}
