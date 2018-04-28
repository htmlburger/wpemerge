<?php

namespace WPEmerge\Responses;

use WPEmerge\Facades\Framework;
use WPEmerge\Facades\Response as ResponseFacade;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide responses dependencies.
 *
 * @codeCoverageIgnore
 */
class ResponsesServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_RESPONSE_SERVICE_KEY ] = function( $c ) {
			return new ResponseService( $c[ WPEMERGE_REQUEST_KEY ] );
		};

		Framework::facade( 'Response', ResponseFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
