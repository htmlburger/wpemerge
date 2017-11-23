<?php

namespace Obsidian\Templating;

use Obsidian\Framework;
use Obsidian\ServiceProviders\ServiceProviderInterface;

/**
 * Provide templating dependencies
 *
 * @codeCoverageIgnore
 */
class TemplatingServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container['framework.templating.engine'] = function() {
			return new \Obsidian\Templating\Php();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
