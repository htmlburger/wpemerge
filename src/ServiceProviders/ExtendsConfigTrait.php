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
use WPEmerge\Support\Arr;

/**
 * Allows objects to extend the config.
 */
trait ExtendsConfigTrait {
	/**
	 * Recursively replace default values with the passed config.
	 * - If either value is not an array, the config value will be used.
	 * - If both are an indexed array, the config value will be used.
	 * - If either is a keyed array, array_replace will be used with config having priority.
	 *
	 * @param  mixed $default
	 * @param  mixed $config
	 * @return mixed
	 */
	protected function replaceConfig( $default, $config ) {
		if ( ! is_array( $default ) || ! is_array( $config ) ) {
			return $config;
		}

		$default_is_indexed = array_keys( $default ) === range( 0, count( $default ) - 1 );
		$config_is_indexed = array_keys( $config ) === range( 0, count( $config ) - 1 );

		if ( $default_is_indexed && $config_is_indexed ) {
			return $config;
		}

		$result = $default;

		foreach ( $config as $key => $value ) {
			$result[ $key ] = $this->replaceConfig( Arr::get( $default, $key ), $value );
		}

		return $result;
	}

	/**
	 * Extends the WP Emerge config in the container with a new key.
	 *
	 * @param  Container $container
	 * @param  string    $key
	 * @param  mixed     $default
	 * @return void
	 */
	public function extendConfig( $container, $key, $default ) {
		$config = isset( $container[ WPEMERGE_CONFIG_KEY ] ) ? $container[ WPEMERGE_CONFIG_KEY ] : [];
		$config = Arr::get( $config, $key, $default );

		$container[ WPEMERGE_CONFIG_KEY ] = array_merge(
			$container[ WPEMERGE_CONFIG_KEY ],
			[$key => $this->replaceConfig( $default, $config )]
		);
	}
}
