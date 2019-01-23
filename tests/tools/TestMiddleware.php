<?php

namespace WPEmergeTestTools;

use Closure;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\RequestInterface;

class TestMiddleware implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {
		return $next( $request );
	}
}
