<?php

namespace Obsidian\Routing;

use Obsidian\Request;
use Obsidian\Middleware\HasMiddlewareInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasMiddlewareInterface {
	/**
	 * Get whether the route is satisfied
	 *
	 * @param  Request $request
	 * @return boolean
	 */
	public function isSatisfied( Request $request );

	/**
	 * Get a response for the given request
	 *
	 * @param  Request                             $request
	 * @param  string                              $template
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( Request $request, $template );
}
