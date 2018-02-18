<?php

namespace WPEmerge\Exceptions;

use Exception as PhpException;
use WPEmerge\Facades\Response;

class ExceptionHandler implements ExceptionHandlerInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( PhpException $e ) {
		if ( $e instanceof NotFoundException ) {
			return Response::error( 404 );
		}

		throw $e;
	}
}
