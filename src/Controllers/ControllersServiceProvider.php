<?php

namespace Obsidian\Controllers;

use Obsidian\Framework;
use Obsidian\ServiceProviders\ServiceProviderInterface;

/**
 * Provide controller dependencies
 *
 * @codeCoverageIgnore
 */
class ControllersServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WordPress::class ] = function() {
			return new WordPress();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
