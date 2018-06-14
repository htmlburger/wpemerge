<?php

namespace WPEmerge\Exceptions;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use WPEmerge\Facades\Response;
use WPEmerge\Facades\Framework;
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
		$this->extendConfig( $container, 'debug', [
			'pretty_stack_trace' => true,
		] );

		$container['whoops'] = function( $container ) {
			$run = new Run();
			$run->allowQuit( false );
			$run->pushHandler( $container['whoops.error_page_handler'] );
			return $run;
		};

		$container['whoops.error_page_handler'] = function() {
			return new PrettyPageHandler();
		};

		$container['whoops.exception_handler'] = $container->protect( function( $exception ) use ( $container ) {
			$method = Run::EXCEPTION_HANDLER;
			ob_start();
			$container['whoops']->$method( $exception );
			$response = ob_get_clean();
			return Response::output( $response )->withStatus( 500 );
		} );

		$container[ WPEMERGE_EXCEPTIONS_EXCEPTION_HANDLER_KEY ] = function( $c ) {
			$stack_trace_handler = $c[ WPEMERGE_CONFIG_KEY ]['debug']['pretty_stack_trace'] ? $c['whoops.exception_handler'] : null;
			return new ExceptionHandler( Framework::debugging(), $stack_trace_handler );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
