<?php

namespace WPEmerge\Csrf;

use WPEmerge\Facades\Framework;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide CSRF dependencies.
 *
 * @codeCoverageIgnore
 */
class CsrfServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_CSRF_KEY ] = function() {
			return new Csrf();
		};

		Framework::facade( 'Csrf', \WPEmerge\Facades\Csrf::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
