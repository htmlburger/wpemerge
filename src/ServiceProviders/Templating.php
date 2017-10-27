<?php

namespace CarbonFramework\ServiceProviders;

use CarbonFramework\Framework;

/**
 * Provide templating dependencies
 */
class Templating implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container['framework.templating.engine'] = function() {
			return new \CarbonFramework\Templating\Php();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
