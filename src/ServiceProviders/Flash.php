<?php

namespace CarbonFramework\ServiceProviders;

use CarbonFramework\Framework;

/**
 * Provide flash dependencies
 */
class Flash implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container['framework.flash.flash'] = function( $c ) {
			$session = null;
			if ( isset( $c['framework.session'] ) ) {
				$session = $c['framework.session'];
			} else if ( isset( $_SESSION ) ) {
				$session = &$_SESSION;
			}
			return new \CarbonFramework\Flash\Flash( $session );
		};

		Framework::facade( 'Flash', \CarbonFramework\Facades\Flash::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}