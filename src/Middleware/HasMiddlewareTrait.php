<?php

namespace Obsidian\Middleware;

use Closure;
use Exception;
use Obsidian\Helpers\Mixed;

/**
 * Allow objects to have middleware
 */
trait HasMiddlewareTrait {
	/**
	 * Array of all registered middleware
	 *
	 * @var array
	 */
	protected $middleware = [];

	/**
	 * Check if the passed entity is a valid middleware
	 *
	 * @param  mixed   $middleware
	 * @return boolean
	 */
	protected function isMiddleware( $middleware ) {
		if ( is_a( $middleware, Closure::class ) ) {
			return true;
		}

		return is_a( $middleware, MiddlewareInterface::class, true );
	}

	/**
	 * Get registered middleware
	 *
	 * @return array
	 */
	public function getMiddleware() {
		return $this->middleware;
	}

	/**
	 * Add middleware.
	 * Accepts: a class name, an instance of a class, a Closure or an array of any of the previous
	 *
	 * @param  string|\Closure|\Obsidian\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function addMiddleware( $middleware ) {
		$middleware = Mixed::toArray( $middleware );

		foreach ( $middleware as $item ) {
			if ( ! $this->isMiddleware( $item ) ) {
				throw new Exception( 'Passed middleware must be a closure or the name or instance of a class which implements the ' . MiddlewareInterface::class . ' interface.' );
			}
		}

		$this->middleware = array_merge( $this->getMiddleware(), $middleware );
		return $this;
	}

	/**
	 * Alias for addMiddleware.
	 * Accepts: a class name, an instance of a class, a Closure or an array of any of the previous
	 *
	 * @codeCoverageIgnore
	 * @param  string|\Closure|\Obsidian\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function add( $middleware ) {
		return call_user_func_array( [$this, 'addMiddleware'], func_get_args() );
	}

	/**
	 * Execute an array of middleware recursively (last in, first out)
	 *
	 * @param  array                               $middleware
	 * @param  \Obsidian\Request                   $request
	 * @param  Closure                             $next
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function executeMiddleware( $middleware, $request, Closure $next ) {
		$top_middleware = array_pop( $middleware );

		if ( $top_middleware === null ) {
			return $next( $request );
		}

		$top_middleware_next = function( $request ) use ( $middleware, $next ) {
			return $this->executeMiddleware( $middleware, $request, $next );
		};

		return Mixed::value( $top_middleware, [$request, $top_middleware_next], 'handle' );
	}
}
