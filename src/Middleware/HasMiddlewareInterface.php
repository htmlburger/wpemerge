<?php

namespace Obsidian\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface for HasMiddlewareTrait
 */
interface HasMiddlewareInterface {
	/**
	 * Get registered middleware
	 *
	 * @return array
	 */
	public function getMiddleware();

	/**
	 * Add middleware
	 *
	 * @param  string|callable|\Obsidian\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function addMiddleware( $middleware );

	/**
	 * Alias for addMiddleware
	 *
	 * @param  string|callable|\Obsidian\Middleware\middlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function add( $middleware );

	/**
	 * Execute an array of middleware recursively (last in, first out)
	 *
	 * @param  array             $middleware
	 * @param  \Obsidian\Request $request
	 * @param  Closure           $next
	 * @return ResponseInterface
	 */
	public function executeMiddleware( $middleware, $request, Closure $next );
}
