<?php

namespace CarbonFramework\Routing;

use Closure;
use Psr\Http\Message\RequestInterface;

interface MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next );
}
