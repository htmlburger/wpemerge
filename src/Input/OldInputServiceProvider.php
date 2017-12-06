<?php

namespace WPEmerge\Input;

use WPEmerge;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide old input dependencies
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

		$container[ WPEMERGE_OLD_INPUT_KEY ] = function() {
			return new \WPEmerge\Input\OldInput();
		};

		WPEmerge::facade( 'OldInput', \WPEmerge\Input\OldInputFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
