<?php

namespace WPEmerge\View;

use WPEmerge;
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
		$container[ WPEMERGE_VIEW_KEY ] = function( $c ) {
			return new \WPEmerge\View\View();
		};

		$container[ WPEMERGE_VIEW_ENGINE_PHP_KEY ] = function( $c ) {
			return new \WPEmerge\View\Php();
		};

		$container[ WPEMERGE_VIEW_ENGINE_KEY ] = $container->raw( WPEMERGE_VIEW_ENGINE_PHP_KEY );

		WPEmerge::facade( 'View', \WPEmerge\View\ViewFacade::class );
		WPEmerge::facade( 'ViewEngine', \WPEmerge\View\ViewEngineFacade::class );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
