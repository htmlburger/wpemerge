<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Flash;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide flash dependencies.
 *
 * @codeCoverageIgnore
 */
class FlashServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_FLASH_KEY ] = function ( $c ) {
			$session = null;
			if ( isset( $c[ WPEMERGE_SESSION_KEY ] ) ) {
				$session = &$c[ WPEMERGE_SESSION_KEY ];
			} else if ( isset( $_SESSION ) ) {
				$session = &$_SESSION;
			}
			return new Flash( $session );
		};

		$container[ FlashMiddleware::class ] = function ( $c ) {
			return new FlashMiddleware( $c[ WPEMERGE_FLASH_KEY ] );
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'flash', WPEMERGE_FLASH_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
