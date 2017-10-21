<?php

namespace CarbonFramework\ServiceProviders;

use CarbonFramework\Framework;
use CarbonFramework\Routing\Middleware\OldInput as OldInputMiddleware;

class OldInput implements ServiceProviderInterface {
	public function register( $container ) {
		$container['framework.global_middleware'] = array_merge( $container['framework.global_middleware'], [
			OldInputMiddleware::class,
		] );

		$container['framework.old_input.old_input'] = function( $c ) {
			return new \CarbonFramework\Input\OldInput();
		};

		Framework::facade( 'OldInput', \CarbonFramework\Facades\OldInput::class );
	}

	public function boot( $container ) {
		// nothing to boot
	}
}