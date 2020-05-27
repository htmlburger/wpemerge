<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Exceptions;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use WPEmerge\Exceptions\Whoops\DebugDataProvider;
use WPEmerge\ServiceProviders\ExtendsConfigTrait;
use WPEmerge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide exceptions dependencies.
 *
 * @codeCoverageIgnore
 */
class ExceptionsServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

		$this->extendConfig( $container, 'debug', [
			'enable' => $debug,
			'pretty_errors' => $debug,
		] );

		$container[ DebugDataProvider::class ] = function ( $container ) {
			return new DebugDataProvider( $container );
		};

		$container[ PrettyPageHandler::class ] = function ( $container ) {
			$handler = new PrettyPageHandler();
			$handler->addResourcePath( implode( DIRECTORY_SEPARATOR, [WPEMERGE_DIR, 'src', 'Exceptions', 'Whoops'] ) );

			$handler->addDataTableCallback( 'WP Emerge: Route', function ( $inspector ) use ( $container ) {
				return $container[ DebugDataProvider::class ]->route( $inspector );
			} );

			return $handler;
		};

		$container[ Run::class ] = function ( $container ) {
			if ( ! class_exists( Run::class ) ) {
				return null;
			}

			$run = new Run();
			$run->allowQuit( false );

			$handler = $container[ PrettyPageHandler::class ];

			if ( $handler ) {
				$run->pushHandler( $handler );
			}

			return $run;
		};

		$container[ WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY ] = function ( $container ) {
			$debug = $container[ WPEMERGE_CONFIG_KEY ]['debug'];
			$whoops = $debug['pretty_errors'] ? $container[ Run::class ] : null;
			return new ErrorHandler( $container[ WPEMERGE_RESPONSE_SERVICE_KEY ], $whoops, $debug['enable'] );
		};

		$container[ WPEMERGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY ] = function ( $container ) {
			$debug = $container[ WPEMERGE_CONFIG_KEY ]['debug'];
			$whoops = $debug['pretty_errors'] ? $container[ Run::class ] : null;
			return new ErrorHandler( $container[ WPEMERGE_RESPONSE_SERVICE_KEY ], $whoops, $debug['enable'] );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
