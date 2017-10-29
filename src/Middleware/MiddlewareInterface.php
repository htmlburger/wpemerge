<?php

namespace CarbonFramework\Middleware;

use Closure;

/**
 * Interface that middleware must implement
 */
interface MiddlewareInterface {
	/**
	 * Execute middleware
	 *
	 * @param  \CarbonFramework\Request            $request
	 * @param  Closure                             $next
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( $request, Closure $next );
}
