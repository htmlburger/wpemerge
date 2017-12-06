<?php

namespace WPEmerge\Controllers;

use WPEmerge\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Handles normal WordPress requests without interfering
 * Useful if you only want to add a middleware to a route without handling the output
 *
 * @codeCoverageIgnore
 */
class WordPress {
	/**
	 * Default WordPress handler
	 *
	 * @param  Request           $request
	 * @param  string            $template
	 * @return ResponseInterface
	 */
	public function handle( Request $request, $template ) {
		return wpm_template( $template )->withStatus( http_response_code() );
	}
}
