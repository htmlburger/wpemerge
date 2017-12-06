<?php

namespace WPEmerge\Templating;

use WPEmerge;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

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
		$container[ WP_EMERGE_TEMPLATING_ENGINE_PHP_KEY ] = function() {
			return new \WPEmerge\Templating\Php();
		};
		$container[ WP_EMERGE_TEMPLATING_ENGINE_KEY ] = $container->raw( WP_EMERGE_TEMPLATING_ENGINE_PHP_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
