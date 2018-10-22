<?php

namespace WPEmerge\View;

use WPEmerge\Facades\Framework;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide view dependencies
 *
 * @codeCoverageIgnore
 */
class ViewServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_VIEW_SERVICE_KEY ] = function () {
			return new \WPEmerge\View\ViewService();
		};

		$container[ WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY ] = function () {
			return new \WPEmerge\View\PhpViewEngine();
		};

		$container[ WPEMERGE_VIEW_ENGINE_KEY ] = $container->raw( WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY );

		Framework::facade( 'View', \WPEmerge\Facades\View::class );
		Framework::facade( 'ViewEngine', \WPEmerge\Facades\ViewEngine::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
