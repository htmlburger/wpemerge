<?php

namespace WPEmergeTestTools;

use Closure;
use WPEmerge\Middleware\MiddlewareInterface;

class TestMiddleware implements MiddlewareInterface {
	public function handle( $request, Closure $next ) {
		return $next( $request );
	}
}
