<?php

namespace WPEmerge\Flash;

use WPEmerge\Facades\Framework;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide flash dependencies
 *
 * @codeCoverageIgnore
 */
class FlashServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_FLASH_KEY ] = function( $c ) {
			$session = null;
			if ( isset( $c[ WPEMERGE_SESSION_KEY ] ) ) {
				$session = $c[ WPEMERGE_SESSION_KEY ];
			} else if ( isset( $_SESSION ) ) {
				$session = &$_SESSION;
			}
			return new \WPEmerge\Flash\Flash( $session );
		};

		Framework::facade( 'Flash', \WPEmerge\Facades\Flash::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
