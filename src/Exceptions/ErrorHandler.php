<?php

namespace WPEmerge\Exceptions;

use Exception as PhpException;
use Psr\Http\Message\ResponseInterface;
use Whoops\RunInterface;
use WPEmerge\Csrf\InvalidCsrfTokenException;
use WPEmerge\Facades\Response;
use WPEmerge\Routing\NotFoundException;

class ErrorHandler implements ErrorHandlerInterface {
	/**
	 * Pretty handler.
	 *
	 * @var RunInterface|null
	 */
	protected $whoops = null;

	/**
	 * Whether debug mode is enabled.
	 *
	 * @var boolean
	 */
	protected $debug = false;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param RunInterface|null $whoops
	 * @param boolean           $debug
	 */
	public function __construct( $whoops, $debug = false ) {
		$this->whoops = $whoops;
		$this->debug = $debug;
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function register() {
		if ( $this->whoops !== null ) {
			$this->whoops->register();
		}
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function unregister() {
		if ( $this->whoops !== null ) {
			$this->whoops->unregister();
		}
	}

	/**
	 * Convert an exception to a ResponseInterface instance if possible.
	 *
	 * @param  PhpException            $exception
	 * @return ResponseInterface|false
	 */
	protected function toResponse( $exception ) {
		// @codeCoverageIgnoreStart
		if ( $exception instanceof InvalidCsrfTokenException ) {
			wp_nonce_ays( '' );
		}
		// @codeCoverageIgnoreEnd

		if ( $exception instanceof NotFoundException ) {
			return Response::error( 404 );
		}

		return false;
	}

	/**
	 * Convert an exception to a pretty error response.
	 *
	 * @codeCoverageIgnore
	 * @param  PhpException      $exception
	 * @return ResponseInterface
	 */
	protected function toPrettyErrorResponse( $exception ) {
		$method = RunInterface::EXCEPTION_HANDLER;
		ob_start();
		$this->whoops->$method( $exception );
		$response = ob_get_clean();
		return Response::output( $response )->withStatus( 500 );
	}

	/**
	 * {@inheritDoc}
	 * @throws PhpException
	 */
	public function getResponse( PhpException $exception ) {
		$response = $this->toResponse( $exception );

		if ( $response !== false ) {
			return $response;
		}

		if ( ! $this->debug ) {
			return Response::error( 500 );
		}

		if ( $this->whoops !== null ) {
			return $this->toPrettyErrorResponse( $exception );
		}

		throw $exception;
	}
}
