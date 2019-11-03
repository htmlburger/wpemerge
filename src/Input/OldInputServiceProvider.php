<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Input;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide old input dependencies.
 *
 * @codeCoverageIgnore
 */
class OldInputServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_OLD_INPUT_KEY ] = function ( $c ) {
			return new OldInput( $c[ WPEMERGE_FLASH_KEY ] );
		};

		$container[ OldInputMiddleware::class ] = function ( $c ) {
			return new OldInputMiddleware( $c[ WPEMERGE_OLD_INPUT_KEY ] );
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'oldInput', WPEMERGE_OLD_INPUT_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
