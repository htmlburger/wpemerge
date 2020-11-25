<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use WPEmerge\Helpers\HandlerFactory;
use WPEmerge\Helpers\MixedType;
use WPEmerge\ServiceProviders\ExtendsConfigTrait;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide application dependencies.
 *
 * @codeCoverageIgnore
 */
class ApplicationServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$this->extendConfig( $container, 'providers', [] );
		$this->extendConfig( $container, 'namespace', 'App\\' );

		$upload_dir = wp_upload_dir();
		$cache_dir = MixedType::addTrailingSlash( $upload_dir['basedir'] ) . 'wpemerge' . DIRECTORY_SEPARATOR . 'cache';
		$this->extendConfig( $container, 'cache', [
			'path' => $cache_dir,
		] );

		$container[ WPEMERGE_APPLICATION_GENERIC_FACTORY_KEY ] = function ( $c ) {
			return new GenericFactory( $c );
		};

		$container[ WPEMERGE_APPLICATION_CLOSURE_FACTORY_KEY ] = function ( $c ) {
			return new ClosureFactory( $c[ WPEMERGE_APPLICATION_GENERIC_FACTORY_KEY ] );
		};

		$container[ WPEMERGE_HELPERS_HANDLER_FACTORY_KEY ] = function ( $c ) {
			return new HandlerFactory( $c[ WPEMERGE_APPLICATION_GENERIC_FACTORY_KEY ] );
		};

		$container[ WPEMERGE_APPLICATION_FILESYSTEM_KEY ] = function ( $c ) {
			global $wp_filesystem;

			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();

			return $wp_filesystem;
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'app', WPEMERGE_APPLICATION_KEY );
		$app->alias( 'closure', WPEMERGE_APPLICATION_CLOSURE_FACTORY_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		$cache_dir = $container[ WPEMERGE_CONFIG_KEY ]['cache']['path'];
		wp_mkdir_p( $cache_dir );
	}
}
