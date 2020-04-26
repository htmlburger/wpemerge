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
use WPEmerge\Support\Arr;

/**
 * Provides static access to an application instance.
 *
 * @codeCoverageIgnore
 * @mixin PortalMixin
 */
class Portal {
	/**
	 * Array of registered instances.
	 *
	 * @var array<string, object>
	 */
	public static $instances = [];

	/**
	 * Make a new application instance and associate it with the current portal.
	 *
	 * @return Application
	 */
	public static function make() {
		$application = Application::make();

		static::setApplication( $application );

		return $application;
	}

	/**
	 * Get the portal application.
	 *
	 * @return object|null
	 */
	public static function getApplication() {
		return Arr::get( static::$instances, static::class, null );
	}

	/**
	 * Set the portal application.
	 *
	 * @param  object|null $application
	 * @return void
	 */
	public static function setApplication( $application ) {
		if ( $application !== null ) {
			static::$instances[ static::class ] = $application;
		} else {
			unset( static::$instances[ static::class ] );
		}
	}

	/**
	 * Invoke any matching instance alias for the static method used.
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
				'Application instance not registered with portal: ' . static::class . '. ' .
				'Did you miss to call ' . static::class . '::make()->bootstrap()?'
			);
		}

		if ( ! is_callable( $callable ) ) {
			throw new BadMethodCallException( "Method {$method} does not exist." );
		}

		return call_user_func_array( $callable, $parameters );
	}
}
