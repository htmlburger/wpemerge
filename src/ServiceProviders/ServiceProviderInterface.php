<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
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
