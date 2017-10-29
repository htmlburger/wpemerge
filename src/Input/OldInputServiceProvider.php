<?php

namespace CarbonFramework\Input;

use CarbonFramework\Framework;
use CarbonFramework\ServiceProviders\ServiceProviderInterface;

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
		$container['framework.routing.global_middleware'] = array_merge( $container['framework.routing.global_middleware'], [
			\CarbonFramework\Input\OldInputMiddleware::class,
		] );

		$container['framework.old_input.old_input'] = function() {
			return new \CarbonFramework\Input\OldInput();
		};

		Framework::facade( 'OldInput', \CarbonFramework\Input\OldInputFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
