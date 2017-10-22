<?php

namespace CarbonFramework\Routing;

use CarbonFramework\Routing\Middleware\HasMiddlewareInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasMiddlewareInterface {
	/**
	 * Return whether the route is satisfied
	 * 
	 * @return boolean
	 */
	public function satisfied();

	/**
	 * Return a response for the given request
	 * 
	 * @param  \CarbonFramework\Request            $request
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( $request );
}
