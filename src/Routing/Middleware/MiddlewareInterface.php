<?php

namespace CarbonFramework\Routing\Middleware;

use Closure;

interface MiddlewareInterface {
	public function handle( $request, Closure $next );
}
