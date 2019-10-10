<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Exceptions;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use WPEmerge\Facades\Application;
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
		$app = $container[ WPEMERGE_APPLICATION_KEY ];

		$this->extendConfig( $container, 'debug', [
			'pretty_errors' => true,
		] );

		$container['whoops'] = function () {
			if ( ! class_exists( Run::class ) ) {
				return null;
			}

			$handler = new PrettyPageHandler();
			$handler->addResourcePath( implode( DIRECTORY_SEPARATOR, [WPEMERGE_DIR, 'src', 'Exceptions', 'Whoops'] ) );

			$run = new Run();
			$run->allowQuit( false );
			$run->pushHandler( $handler );
			return $run;
		};

		$container[ WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY ] = function ( $c ) use ( $app ) {
			$whoops = $c[ WPEMERGE_CONFIG_KEY ]['debug']['pretty_errors'] ? $c['whoops'] : null;
			return new ErrorHandler( $c[ WPEMERGE_RESPONSE_SERVICE_KEY ], $whoops, $app->debugging() );
		};

		$container[ WPEMERGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY ] = function ( $c ) use ( $app ) {
			$whoops = $c[ WPEMERGE_CONFIG_KEY ]['debug']['pretty_errors'] ? $c['whoops'] : null;
			return new ErrorHandler( $c[ WPEMERGE_RESPONSE_SERVICE_KEY ], $whoops, $app->debugging() );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
