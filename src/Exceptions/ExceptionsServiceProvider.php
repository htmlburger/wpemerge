<?php

namespace WPEmerge\Exceptions;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide exceptions dependencies.
 *
 * @codeCoverageIgnore
 */
class ExceptionsServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_EXCEPTIONS_EXCEPTION_HANDLER_KEY ] = function() {
			return new ExceptionHandler();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
