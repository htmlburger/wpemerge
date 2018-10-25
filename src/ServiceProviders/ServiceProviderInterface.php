<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\ServiceProviders;

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
