<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\View;

use WPEmerge\Facades\Application;
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

		$container[ WPEMERGE_VIEW_SERVICE_KEY ] = function () {
			return new ViewService();
		};

		$container[ WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY ] = function ( $c ) {
			return new PhpViewEngine( $c[ WPEMERGE_CONFIG_KEY ]['views'] );
		};

		$container[ WPEMERGE_VIEW_ENGINE_KEY ] = $container->raw( WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY );

		Application::alias( 'View', \WPEmerge\Facades\View::class );
		Application::alias( 'ViewEngine', \WPEmerge\Facades\ViewEngine::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
