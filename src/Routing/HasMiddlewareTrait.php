<?php

namespace CarbonFramework\Routing;

use Closure;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
	 * @param  any     $middleware
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
	 * @return array
	 */
	public function getMiddleware() {
		return $this->middleware;
	}

	/**
	 * Add middleware
	 *
	 * @param  string|callable|array $middleware
	 * @return object
	 */
	public function addMiddleware( $middleware ) {
		$middleware = is_array( $middleware ) ? $middleware : [$middleware];

		foreach ( $middleware as $layer ) {
			if ( ! $this->isMiddleware( $layer ) ) {
				throw new Exception( 'Passed middleware must be a callable or the name of a class which implements the ' . MiddlewareInterface::class . ' interface.' );
			}
		}

		$this->middleware = array_merge( $this->getMiddleware(), $middleware );
		return $this;
	}

	/**
	 * Alias for addMiddleware
	 *
	 * @param  string|callable|array $middleware
	 * @return object
	 */
	public function add( $middleware ) {
		return $this->addMiddleware( $middleware );
	}

	/**
	 * Execute all registered middleware last in, first out
	 * 
	 * @param  RequestInterface  $request
	 * @param  Closure           $next
	 * @return ResponseInterface
	 */
	public function executeMiddleware( RequestInterface $request, Closure $next ) {
		return $this->executeMiddlewareLayers( $this->getMiddleware(), $request, $next );
	}

	/**
	 * Execute middleware layers recursively last in, first out
	 * 
	 * @param  array             $layers
	 * @param  RequestInterface  $request
	 * @param  Closure           $next
	 * @return ResponseInterface
	 */
	protected function executeMiddlewareLayers( $layers, RequestInterface $request, Closure $next ) {
		$top_layer = array_pop( $layers );

		if ( $top_layer === null ) {
			return $next( $request );
		}

		$top_layer_next = function( $request ) use ( $layers, $next ) {
			return $this->executeMiddlewareLayers( $layers, $request, $next );
		};

		if ( is_callable( $top_layer ) ) {
			return call_user_func( $top_layer, $request, $top_layer_next );
		}

		$instance = new $top_layer();
		return $instance->handle( $request, $top_layer_next );
	}
}
