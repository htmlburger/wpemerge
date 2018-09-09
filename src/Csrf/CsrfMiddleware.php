<?php

namespace WPEmerge\Csrf;

use Closure;
use WPEmerge\Facades\Csrf as CsrfService;
use WPEmerge\Middleware\MiddlewareInterface;

/**
 * Store current request data and clear old request data
 */
class CsrfMiddleware implements MiddlewareInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( $request, Closure $next ) {
		if ( ! $request->isReadVerb() ) {
			$token = CsrfService::getTokenFromRequest( $request );
			if ( ! CsrfService::isValidToken( $token ) ) {
				throw new InvalidCsrfTokenException();
			}
		}

		CsrfService::generateToken();

		return $next( $request );
	}
}
