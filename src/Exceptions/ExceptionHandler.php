<?php

namespace WPEmerge\Exceptions;

use Exception;
use WPEmerge\Facades\Response;

class ExceptionHandler implements ExceptionHandlerInterface {
	/**
	 * {@inheritDoc}
	 */
	public function handle( Exception $e ) {
		if ( $e instanceof NotFoundException ) {
			return Response::error( 404 );
		}

		throw $e;
	}
}
