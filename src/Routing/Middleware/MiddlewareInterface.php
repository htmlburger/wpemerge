<?php

namespace CarbonFramework\Routing\Middleware;

use Closure;

/**
 * Interface that middleware must implement
 */
interface MiddlewareInterface {
	/**
	 * Execute middleware
	 *
	 * @param \CarbonFramework\Request $request
	 * @param  Closure                 $next
	 */
	public function handle( $request, Closure $next );
}
