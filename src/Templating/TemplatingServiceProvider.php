<?php

namespace Obsidian\Templating;

use Obsidian;
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
		$container['framework.templating.engine.php'] = function() {
			return new \Obsidian\Templating\Php();
		};
		$container['framework.templating.engine'] = $container->raw( 'framework.templating.engine.php' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
