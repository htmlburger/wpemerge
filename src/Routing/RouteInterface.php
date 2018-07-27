<?php

namespace WPEmerge\Routing;

use WPEmerge\Requests\Request;
use WPEmerge\Middleware\HasMiddlewareInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasMiddlewareInterface {
	/**
	 * Get whether the route is satisfied.
	 *
	 * @param  Request $request
	 * @return boolean
	 */
	public function isSatisfied( Request $request );

	/**
	 * Get a response for the given request.
	 *
	 * @param  Request                             $request
	 * @param  string                              $view
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( Request $request, $view );

	/**
	 * Get arguments.
	 *
	 * @param  Request $request
	 * @return array
	 */
	public function getArguments( Request $request );
}
