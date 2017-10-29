<?php

namespace CarbonFramework\Input;

use Closure;
use Flash;
use OldInput;
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
			Flash::clear( OldInput::getFlashKey() );

			if ( $request->getMethod() === 'POST' ) {
				Flash::add( OldInput::getFlashKey(), $request->post() );
			}
		}

		return $response;
	}
}
