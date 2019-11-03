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

		if ( $config !== $default && is_array( $config ) && is_array( $default ) ) {
			$config = array_replace_recursive( $default, $config );
		}

		$container[ WPEMERGE_CONFIG_KEY ] = array_merge(
			$container[ WPEMERGE_CONFIG_KEY ],
			[$key => $config]
		);
	}
}
