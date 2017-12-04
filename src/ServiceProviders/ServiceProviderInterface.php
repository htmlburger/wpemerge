<?php

namespace Obsidian\ServiceProviders;

/**
 * Interface that service providers must implement
 *
 * @codeCoverageIgnore
 */
interface ServiceProviderInterface {
	/**
	 * Register all dependencies in the IoC container
	 *
	 * @param  \Pimple\Container $container
	 * @return void
	 */
	public function register( $container );

	/**
	 * Bootstrap any services if needed
	 *
	 * @param  \Pimple\Container $container
	 * @return void
	 */
	public function boot( $container );
}
