<?php

namespace CarbonFramework\ServiceProviders;

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
	 * @return null
	 */
	public function register( $container );

	/**
	 * Bootstrap any services if needed
	 *
	 * @param  \Pimple\Container $container
	 * @return null
	 */
	public function boot( $container );
}
