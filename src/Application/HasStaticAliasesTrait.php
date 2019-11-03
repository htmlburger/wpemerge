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
	 * Instances.
	 *
	 * @var array<string, static>
	 */
	protected static $instances = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		static::$instances[ static::class ] = $this;
	}

	/**
	 * Get instance.
	 *
	 * @param  string $class
	 * @return static
	 */
	protected static function instance( $class ) {
		return isset( static::$instances[ $class ] ) ? static::$instances[ $class ] : null;
	}

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
