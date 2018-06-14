<?php

namespace WPEmerge\Exceptions;

use Exception as PhpException;
use WPEmerge\Facades\Response;

class ExceptionHandler implements ExceptionHandlerInterface {
	/**
	 * Whether debug mode is enabled.
	 *
	 * @var boolean
	 */
	protected $debug = false;

	/**
	 * Stack trace handler in debug mode.
	 *
	 * @var callable
	 */
	protected $stack_trace_handler = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param boolean       $debug
	 * @param callable|null $stack_trace_handler
	 */
	public function __construct( $debug = false, $stack_trace_handler = null ) {
		$this->debug = $debug;
		$this->stack_trace_handler = is_callable( $stack_trace_handler ) ? $stack_trace_handler : array( $this, 'rethrow' );
	}

	/**
	 * Throw an exception.
	 *
	 * @codeCoverageIgnore
	 * @throws PhpException
	 * @param  PhpException $exception;
	 * @return void
	 */
	protected function rethrow( PhpException $exception ) {
		throw $exception;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( PhpException $exception ) {
		// @codeCoverageIgnoreStart
		if ( $exception instanceof InvalidCsrfTokenException ) {
			wp_nonce_ays( '' );
		}
		// @codeCoverageIgnoreEnd

		if ( $exception instanceof NotFoundException ) {
			return Response::error( 404 );
		}

		if ( $this->debug ) {
			return call_user_func( $this->stack_trace_handler, $exception );
		}

		$this->rethrow( $exception );
	}
}
