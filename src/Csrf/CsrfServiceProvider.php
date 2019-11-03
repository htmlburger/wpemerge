<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Csrf;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide CSRF dependencies.
 *
 * @codeCoverageIgnore
 */
class CsrfServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_CSRF_KEY ] = function () {
			return new Csrf();
		};

		$container[ CsrfMiddleware::class ] = function ( $c ) {
			return new CsrfMiddleware( $c[ WPEMERGE_CSRF_KEY ] );
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'csrf', WPEMERGE_CSRF_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
