<?php

namespace Obsidian\Flash;

use Obsidian;
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
		$container[ OBSIDIAN_FLASH_KEY ] = function( $c ) {
			$session = null;
			if ( isset( $c[ OBSIDIAN_SESSION_KEY ] ) ) {
				$session = $c[ OBSIDIAN_SESSION_KEY ];
			} else if ( isset( $_SESSION ) ) {
				$session = &$_SESSION;
			}
			return new \Obsidian\Flash\Flash( $session );
		};

		Obsidian::facade( 'Flash', \Obsidian\Flash\FlashFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
