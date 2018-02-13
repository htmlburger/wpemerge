<?php

namespace WPEmerge\Input;

use Closure;
use WPEmerge\Facades\OldInput as OldInputService;
use WPEmerge\Middleware\MiddlewareInterface;

/**
 * Store current request data and clear old request data
 */
class OldInputMiddleware implements MiddlewareInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( $request, Closure $next ) {
		if ( OldInputService::enabled() && $request->isPost() ) {
			OldInputService::set( $request->post() );
		}

		return $next( $request );
	}
}
