<?php

namespace WPEmerge\Exceptions;

use Exception as PhpException;
use WPEmerge\Facades\Response;

class ExceptionHandler implements ExceptionHandlerInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( PhpException $e ) {
		// @codeCoverageIgnoreStart
		if ( $e instanceof InvalidCsrfTokenException ) {
			wp_nonce_ays( '' );
		}
		// @codeCoverageIgnoreEnd

		if ( $e instanceof NotFoundException ) {
			return Response::error( 404 );
		}

		throw $e;
	}
}
