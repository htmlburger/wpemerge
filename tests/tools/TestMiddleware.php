<?php

namespace ObsidianTestTools;

use Closure;
use Obsidian\Middleware\MiddlewareInterface;

class TestMiddleware implements MiddlewareInterface {
	public function handle( $request, Closure $next ) {
		return $next( $request );
	}
}
