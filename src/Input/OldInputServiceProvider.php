<?php

namespace Obsidian\Input;

use Obsidian\Framework;
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
		$container['framework.routing.global_middleware'] = array_merge( $container['framework.routing.global_middleware'], [
			\Obsidian\Input\OldInputMiddleware::class,
		] );

		$container['framework.old_input.old_input'] = function() {
			return new \Obsidian\Input\OldInput();
		};

		Framework::facade( 'OldInput', \Obsidian\Input\OldInputFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
