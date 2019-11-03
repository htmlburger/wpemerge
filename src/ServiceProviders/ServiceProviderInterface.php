<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\ServiceProviders;

use Pimple\Container;

/**
 * Interface that service providers must implement
 */
interface ServiceProviderInterface {
	/**
	 * Register all dependencies in the IoC container.
	 *
	 * @param  Container $container
	 * @return void
	 */
	public function register( $container );

	/**
	 * Bootstrap any services if needed.
	 *
	 * @param  Container $container
	 * @return void
	 */
	public function bootstrap( $container );
}
