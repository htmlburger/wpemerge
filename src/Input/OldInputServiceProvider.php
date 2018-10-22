<?php

namespace WPEmerge\Input;

use WPEmerge\Facades\Framework;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide old input dependencies.
 *
 * @codeCoverageIgnore
 */
class OldInputServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY ] = array_merge(
			$container[ WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY ],
			[
				\WPEmerge\Input\OldInputMiddleware::class,
			]
		);

		$container[ WPEMERGE_ROUTING_MIDDLEWARE_PRIORITY_KEY ] = array_merge(
			$container[ WPEMERGE_ROUTING_MIDDLEWARE_PRIORITY_KEY ],
			[
				\WPEmerge\Input\OldInputMiddleware::class => 15,
			]
		);

		$container[ WPEMERGE_OLD_INPUT_KEY ] = function ( $c ) {
			return new \WPEmerge\Input\OldInput( $c[ WPEMERGE_FLASH_KEY ] );
		};

		Framework::facade( 'OldInput', \WPEmerge\Facades\OldInput::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot.
	}
}
