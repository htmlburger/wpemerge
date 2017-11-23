<?php

namespace Obsidian\Flash;

use Obsidian\Framework;
use Obsidian\ServiceProviders\ServiceProviderInterface;

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
		$container['framework.flash.flash'] = function( $c ) {
			$session = null;
			if ( isset( $c['framework.session'] ) ) {
				$session = $c['framework.session'];
			} else if ( isset( $_SESSION ) ) {
				$session = &$_SESSION;
			}
			return new \Obsidian\Flash\Flash( $session );
		};

		Framework::facade( 'Flash', \Obsidian\Flash\FlashFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
