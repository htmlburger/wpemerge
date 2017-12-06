<?php

namespace Obsidian\Input;

use Obsidian;
use Obsidian\ServiceProviders\ServiceProviderInterface;

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
		$container[ OBSIDIAN_ROUTING_GLOBAL_MIDDLEWARE_KEY ] = array_merge(
			$container[ OBSIDIAN_ROUTING_GLOBAL_MIDDLEWARE_KEY ],
			[
				\Obsidian\Input\OldInputMiddleware::class,
			]
		);

		$container[ OBSIDIAN_OLD_INPUT_KEY ] = function() {
			return new \Obsidian\Input\OldInput();
		};

		Obsidian::facade( 'OldInput', \Obsidian\Input\OldInputFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
