<?php

namespace WPEmerge\Exceptions;

use Exception as PhpException;
use Psr\Http\Message\ResponseInterface;

interface ExceptionHandlerInterface {
	/**
	 * Handle an exception by returning a suitable response or rethrowing it.
	 *
	 * @param  PhpException      $e
	 * @return ResponseInterface
	 */
	public function handle( PhpException $e );
}
