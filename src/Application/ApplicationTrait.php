<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use BadMethodCallException;
use WPEmerge\Exceptions\ConfigurationException;

/**
 * Provides static access to an Application instance.
 *
 * @mixin ApplicationMixin
 */
trait ApplicationTrait {
	/**
	 * Application instance.
	 *
	 * @var Application|null
	 */
	public static $instance = null;

	/**
	 * Make and assign a new application instance.
	 *
	 * @return Application
	 */
	public static function make() {
		static::setApplication( Application::make() );

		return static::getApplication();
	}

	/**
	 * Get the Application instance.
	 *
	 * @codeCoverageIgnore
	 * @return Application|null
	 */
	public static function getApplication() {
		return static::$instance;
	}

	/**
	 * Set the Application instance.
	 *
	 * @codeCoverageIgnore
	 * @param  Application|null $application
	 * @return void
	 */
	public static function setApplication( $application ) {
		static::$instance = $application;
	}

	/**
	 * Invoke any matching instance method for the static method being called.
	 *
	 * @param  string $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public static function __callStatic( $method, $parameters ) {
		$application = static::getApplication();
		$callable = [$application, $method];

		if ( ! $application ) {
			throw new ConfigurationException(
				'Application instance not created in ' . static::class . '. ' .
				'Did you miss to call ' . static::class . '::make()?'
			);
		}

		if ( ! is_callable( $callable ) ) {
			throw new BadMethodCallException( 'Method ' . get_class( $application ) . '::' . $method . '() does not exist.' );
		}

		return call_user_func_array( $callable, $parameters );
	}
}
