<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Responses;

use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide responses dependencies.
 *
 * @codeCoverageIgnore
 */
class ResponsesServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ WPEMERGE_RESPONSE_SERVICE_KEY ] = function ( $c ) {
			return new ResponseService( $c[ WPEMERGE_REQUEST_KEY ], $c[ WPEMERGE_VIEW_SERVICE_KEY ] );
		};

		$app = $container[ WPEMERGE_APPLICATION_KEY ];
		$app->alias( 'responses', WPEMERGE_RESPONSE_SERVICE_KEY );

		$app->alias( 'response', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'response'], func_get_args() );
		} );

		$app->alias( 'output', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'output'], func_get_args() );
		} );

		$app->alias( 'json', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'json'], func_get_args() );
		} );

		$app->alias( 'redirect', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'redirect'], func_get_args() );
		} );

		$app->alias( 'error', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'error'], func_get_args() );
		} );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
