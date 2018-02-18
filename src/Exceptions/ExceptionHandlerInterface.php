<?php

namespace WPEmerge\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

interface ExceptionHandlerInterface {
	/**
	 * Handle an exception by returning a suitable response or rethrowing it.
	 *
	 * @param  Exception         $e
	 * @return ResponseInterface
	 */
	public function handle( Exception $e );
}
