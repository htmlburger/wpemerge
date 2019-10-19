<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use WPEmerge\Helpers\MixedType;
use WPEmerge\ServiceProviders\ExtendsConfigTrait;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide view dependencies
 *
 * @codeCoverageIgnore
 */
class ViewServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		/** @var $container \Pimple\Container */
		$this->extendConfig( $container, 'views', '' );

		$container[ WPEMERGE_VIEW_SERVICE_KEY ] = function ( $c ) {
			return new ViewService( $c[ WPEMERGE_VIEW_ENGINE_KEY ], $c[ WPEMERGE_HELPERS_HANDLER_FACTORY_KEY ] );
		};

		$container[ WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY ] = function ( $c ) {
			$finder = new PhpViewFilesystemFinder( MixedType::toArray( $c[ WPEMERGE_CONFIG_KEY ]['views'] ) );
			return new PhpViewEngine( $finder );
		};

		$container[ WPEMERGE_VIEW_ENGINE_KEY ] = $container->raw( WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY );

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'View', \WPEmerge\Facades\View::class );
		$app->alias( 'ViewEngine', \WPEmerge\Facades\ViewEngine::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
