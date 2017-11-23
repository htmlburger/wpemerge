<?php

namespace Obsidian\Middleware;

use Closure;

/**
 * Interface that middleware must implement
 */
interface MiddlewareInterface {
	/**
	 * Execute middleware
	 *
	 * @param  \Obsidian\Request                   $request
	 * @param  Closure                             $next
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( $request, Closure $next );
}
