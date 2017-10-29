<?php

namespace CarbonFramework\Input;

use Closure;
use Flash;
use OldInput as OldInputService;
use CarbonFramework\Middleware\MiddlewareInterface;

/**
 * Flash current request data and clear old request data
 */
class OldInputMiddleware implements MiddlewareInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( $request, Closure $next ) {
		$response = $next( $request );

		if ( Flash::enabled() ) {
			Flash::clear( OldInputService::getFlashKey() );

			if ( $request->getMethod() === 'POST' ) {
				Flash::add( OldInputService::getFlashKey(), $request->post() );
			}
		}

		return $response;
	}
}
