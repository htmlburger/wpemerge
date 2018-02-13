<?php

namespace WPEmerge\Flash;

use Closure;
use WPEmerge\Facades\Flash as FlashService;
use WPEmerge\Middleware\MiddlewareInterface;

/**
 * Store current request data and clear old request data
 */
class FlashMiddleware implements MiddlewareInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( $request, Closure $next ) {
		$response = $next( $request );

		if ( FlashService::enabled() ) {
			FlashService::shift();
			FlashService::save();
		}

		return $response;
	}
}
