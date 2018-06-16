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
			'pretty_errors' => true,
		] );

		$container['whoops'] = function( $container ) {
			$run = new Run();
			$run->allowQuit( false );
			$run->pushHandler( new PrettyPageHandler() );
			return $run;
		};

		$container[ WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY ] = function( $c ) {
			$whoops = $c[ WPEMERGE_CONFIG_KEY ]['debug']['pretty_errors'] ? $c['whoops'] : null;
			return new ErrorHandler( $whoops, Framework::debugging() );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( $container ) {
		// nothing to boot
	}
}
