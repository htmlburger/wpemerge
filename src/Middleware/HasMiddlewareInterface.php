<?php

namespace CarbonFramework\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface for HasMiddlewareTrait
 */
interface HasMiddlewareInterface {
	/**
	 * Get registered middleware
	 *
	 * @return \CarbonFramework\Middleware\MiddlewareInterface[]
	 */
	public function getMiddleware();

	/**
	 * Add middleware
	 *
	 * @param  string|callable|\CarbonFramework\Middleware\MiddlewareInterface|array $middleware
	 * @return object
	 */
	public function addMiddleware( $middleware );

	/**
	 * Alias for addMiddleware
	 *
	 * @param  string|callable|\CarbonFramework\Middleware\MiddlewareInterface|array $middleware
	 * @return object
	 */
	public function add( $middleware );

	/**
	 * Execute an array of middleware recursively (last in, first out)
	 *
	 * @param  \CarbonFramework\Middleware\MiddlewareInterface[] $middleware
	 * @param  mixed                                             $request
	 * @param  Closure                                           $next
	 * @return ResponseInterface
	 */
	public function executeMiddleware( $middleware, $request, Closure $next );
}
