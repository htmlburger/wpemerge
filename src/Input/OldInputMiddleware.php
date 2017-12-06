<?php

namespace WPEmerge\Input;

use WPEmerge\Middleware\MiddlewareInterface;
use Closure;
use OldInput as OldInputService;

/**
 * Store current request data and clear old request data
 */
class OldInputMiddleware implements MiddlewareInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( $request, Closure $next ) {
		$response = $next( $request );

		OldInputService::clear();
		if ( $request->getMethod() === 'POST' ) {
			OldInputService::store( $request->post() );
		}

		return $response;
	}
}
