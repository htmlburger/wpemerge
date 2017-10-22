<?php

namespace CarbonFramework\ServiceProviders;

use CarbonFramework\Framework;

class Templating implements ServiceProviderInterface {
	public function register( $container ) {
		$container['framework.templating.engine'] = function( $c ) {
			return new \CarbonFramework\Templating\Php();
		};
	}

	public function boot( $container ) {
		// nothing to boot
	}
}