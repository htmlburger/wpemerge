<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use RuntimeException;

/**
 * Allows adding static aliases at runtime.
 *
 * @codeCoverageIgnore
 */
trait HasStaticAliasesTrait {
	use HasAliasesTrait;

	/**
	 * Filter to "store" instances in.
	 *
	 * @var string
	 */
	protected static $instances_filter = 'wpemerge.application.instances';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( static::$instances_filter, [$this, 'registerInstance'] );
	}

	/**
	 * Register the current instance.
	 *
	 * @param  array<string, object> $instances
	 * @return array<string, object>
	 */
	public function registerInstance( $instances ) {
		$instances[ static::class ] = $this;

		return $instances;
	}

	/**
	 * Get instance.
	 *
	 * @param  string $class
	 * @return static
	 */
	protected static function instance( $class ) {
		$instances = apply_filters( static::$instances_filter, [] );

		return isset( $instances[ $class ] ) ? $instances[ $class ] : null;
	}

	/**
	 * Invoke any matching alias when a static method is used.
	 *
	 * @param  string $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public static function __callStatic( $method, $parameters ) {
		$instance = static::instance( static::class );

		if ( ! $instance ) {
			throw new RuntimeException( 'Application instance not bootstrapped: ' . static::class );
		}

		if ( ! $instance->hasAlias( $method ) ) {
			throw new RuntimeException( 'Application alias not found: ' . $method );
		}

		return call_user_func_array( [$instance, $method], $parameters );
	}
}
