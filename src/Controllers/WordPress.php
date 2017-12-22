<?php

namespace WPEmerge\Controllers;

use WPEmerge;
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
	 * @param  string            $view
	 * @return ResponseInterface
	 */
	public function handle( Request $request, $view ) {
		return WPEmerge\view( $view )->withStatus( http_response_code() );
	}
}
