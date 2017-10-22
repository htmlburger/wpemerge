<?php

namespace CarbonFramework\Routing\Middleware;

use Closure;
use GuzzleHttp\Psr7;
use Flash;
use OldInput as OldInputService;

/**
 * Flash current request data and clear old request data
 */
class OldInput implements MiddlewareInterface {
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