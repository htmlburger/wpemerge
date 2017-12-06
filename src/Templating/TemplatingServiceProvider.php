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
		$container[ OBSIDIAN_TEMPLATING_ENGINE_PHP_KEY ] = function() {
			return new \Obsidian\Templating\Php();
		};
		$container[ OBSIDIAN_TEMPLATING_ENGINE_KEY ] = $container->raw( OBSIDIAN_TEMPLATING_ENGINE_PHP_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
