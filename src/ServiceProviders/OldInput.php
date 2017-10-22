<?php

namespace CarbonFramework\ServiceProviders;

use CarbonFramework\Framework;
use CarbonFramework\Routing\Middleware\OldInput as OldInputMiddleware;

/**
 * Provide old input dependencies
 */
class OldInput implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container['framework.global_middleware'] = array_merge( $container['framework.global_middleware'], [
			OldInputMiddleware::class,
		] );

		$container['framework.old_input.old_input'] = function() {
			return new \CarbonFramework\Input\OldInput();
		};

		Framework::facade( 'OldInput', \CarbonFramework\Facades\OldInput::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}