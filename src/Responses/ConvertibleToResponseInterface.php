<?php

namespace WPEmerge\Responses;

use Psr\Http\Message\ResponseInterface;

interface ConvertibleToResponseInterface {
	/**
	 * Convert to Psr\Http\Message\ResponseInterface.
	 *
	 * @return ResponseInterface
	 */
	public function toResponse();
}
