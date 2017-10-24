<?php

namespace CarbonFramework\Routing\Middleware;

use Closure;
use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Allow objects to have middleware
 */
trait HasMiddlewareTrait {
	/**
	 * Array of all registered middleware
	 * 
	 * @var \CarbonFramework\Routing\Middleware\MiddlewareInterface[]
	 */
	protected $middleware = [];

	/**
	 * Check if the passed entity is a valid middleware
	 * 
	 * @param  mixed   $middleware
	 * @return boolean
	 */
	protected function isMiddleware( $middleware ) {
		if ( is_callable( $middleware ) ) {
			return true;
		}
		
		if ( is_a( $middleware, MiddlewareInterface::class, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get registered middleware
	 *
	 * @return \CarbonFramework\Routing\Middleware\MiddlewareInterface[]
	 */
	public function getMiddleware() {
		return $this->middleware;
	}

	/**
	 * Add middleware
	 *
	 * @param  string|callable|\CarbonFramework\Routing\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                                        $this
	 */
	public function addMiddleware( $middleware ) {
		$middleware = is_array( $middleware ) ? $middleware : [$middleware];

		foreach ( $middleware as $item ) {
			if ( ! $this->isMiddleware( $item ) ) {
				throw new Exception( 'Passed middleware must be a callable or the name of a class which implements the ' . MiddlewareInterface::class . ' interface.' );
			}
		}

		$this->middleware = array_merge( $this->getMiddleware(), $middleware );
		return $this;
	}

	/**
	 * Alias for addMiddleware
	 *
	 * @param  string|callable|\CarbonFramework\Routing\Middleware\middlewareInterface|array $middleware
	 * @return object                                                                        $this
	 */
	public function add( $middleware ) {
		return $this->addMiddleware( $middleware );
	}

	/**
	 * Execute an array of middleware recursively (last in, first out)
	 *
	 * @param  \CarbonFramework\Routing\Middleware\MiddlewareInterface[] $middleware
	 * @param  \CarbonFramework\Request                                  $request
	 * @param  Closure                                                   $next
	 * @return ResponseInterface
	 */
	public function executeMiddleware( $middleware, $request, Closure $next ) {
		$top_middleware = array_pop( $middleware );

		if ( $top_middleware === null ) {
			return $next( $request );
		}

		$top_middleware_next = function( $request ) use ( $middleware, $next ) {
			return $this->executeMiddleware( $middleware, $request, $next );
		};

		if ( is_callable( $top_middleware ) ) {
			return call_user_func( $top_middleware, $request, $top_middleware_next );
		}

		$instance = new $top_middleware();
		return $instance->handle( $request, $top_middleware_next );
	}
}
