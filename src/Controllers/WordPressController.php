<?php

namespace WPEmerge\Controllers;

use WPEmerge\Facades\Response;
use WPEmerge\Requests\Request;

/**
 * Handles normal WordPress requests without interfering
 * Useful if you only want to add a middleware to a route without handling the output
 *
 * @codeCoverageIgnore
 */
class WordPressController {
	/**
	 * Default WordPress handler.
	 *
	 * @param  Request                             $request
	 * @param  string                              $view
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( Request $request, $view ) {
		return Response::view( $view )
			->toResponse()
			->withStatus( http_response_code() );
	}
}
