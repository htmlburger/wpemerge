<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide middleware dependencies.
 *
 * @codeCoverageIgnore
 */
class MiddlewareServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ UserLoggedOutMiddleware::class ] = function ( $c ) {
			return new UserLoggedOutMiddleware( $c[ WPEMERGE_RESPONSE_SERVICE_KEY ] );
		};

		$container[ UserLoggedInMiddleware::class ] = function ( $c ) {
			return new UserLoggedInMiddleware( $c[ WPEMERGE_RESPONSE_SERVICE_KEY ] );
		};

		$container[ UserCanMiddleware::class ] = function ( $c ) {
			return new UserCanMiddleware( $c[ WPEMERGE_RESPONSE_SERVICE_KEY ] );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
