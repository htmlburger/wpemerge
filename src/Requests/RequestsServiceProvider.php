<?php

namespace WPEmerge\Requests;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide request dependencies.
 *
 * @codeCoverageIgnore
 */
class RequestsServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_REQUEST_KEY ] = function () {
			return Request::fromGlobals();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
