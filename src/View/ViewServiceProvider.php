<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use Pimple\Container;
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
		/** @var Container $container */
		$namespace = $container[ WPEMERGE_CONFIG_KEY ]['namespace'];

		$this->extendConfig( $container, 'views', [get_stylesheet_directory(), get_template_directory()] );

		$this->extendConfig( $container, 'view_composers', [
			'namespace' => $namespace . 'ViewComposers\\',
		] );

		$container[ WPEMERGE_VIEW_SERVICE_KEY ] = function ( $c ) {
			return new ViewService(
				$c[ WPEMERGE_CONFIG_KEY ]['view_composers'],
				$c[ WPEMERGE_VIEW_ENGINE_KEY ],
				$c[ WPEMERGE_HELPERS_HANDLER_FACTORY_KEY ]
			);
		};

		$container[ WPEMERGE_VIEW_COMPOSE_ACTION_KEY ] = function ( $c ) {
			return function ( ViewInterface $view ) use ( $c ) {
				$view_service = $c[ WPEMERGE_VIEW_SERVICE_KEY ];
				$view_service->compose( $view );
				return $view;
			};
		};

		$container[ WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY ] = function ( $c ) {
			$finder = new PhpViewFilesystemFinder( MixedType::toArray( $c[ WPEMERGE_CONFIG_KEY ]['views'] ) );
			return new PhpViewEngine( $c[ WPEMERGE_VIEW_COMPOSE_ACTION_KEY ], $finder );
		};

		$container[ WPEMERGE_VIEW_ENGINE_KEY ] = function ( $c ) {
			return $c[ WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY ];
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'views', WPEMERGE_VIEW_SERVICE_KEY );

		$app->alias( 'view', function () use ( $app ) {
			return call_user_func_array( [$app->views(), 'make'], func_get_args() );
		} );

		$app->alias( 'render', function () use ( $app ) {
			return call_user_func_array( [$app->views(), 'render'], func_get_args() );
		} );

		$app->alias( 'layoutContent', function () use ( $app ) {
			/** @var PhpViewEngine $engine */
			$engine = $app->resolve( WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY );

			echo $engine->getLayoutContent();
		} );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
