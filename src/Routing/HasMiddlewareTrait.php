<?php

namespace CarbonFramework\Routing;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait HasMiddlewareTrait {
	protected $middleware = [];

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
	 * @param  callable|array<callable> $middleware
	 * @return object
	 */
	public function addMiddleware( $middleware ) {
		$middleware = is_array( $middleware ) ? $middleware : [$middleware];

		foreach ( $middleware as $layer ) {
			if ( ! is_callable( $layer ) ) {
				throw new Exception( 'Passed middleware must be a callable.' );
			}
		}

		$this->middleware = array_merge( $this->getMiddleware(), $middleware );
		return $this;
	}

	/**
	 * Alias for addMiddleware
	 *
	 * @param  callable|array<callable> $middleware
	 * @return object
	 */
	public function add( $middleware ) {
		return $this->addMiddleware( $middleware );
	}

	/**
	 * Execute all registered middleware last in, first out
	 * 
	 * @param  RequestInterface  $request
	 * @param  callable          $next
	 * @return ResponseInterface
	 */
	public function executeMiddleware( RequestInterface $request, $next ) {
		return $this->executeMiddlewareLayers( $this->getMiddleware(), $request, $next );
	}

	/**
	 * Execute middleware layers recursively last in, first out
	 * 
	 * @param  array<callable>   $layers
	 * @param  RequestInterface  $request
	 * @param  callable          $next
	 * @return ResponseInterface
	 */
	protected function executeMiddlewareLayers( $layers, RequestInterface $request, $next ) {
		$top_layer = array_pop( $layers );

		if ( $top_layer === null ) {
			return $next( $request );
		}

		return call_user_func( $top_layer, $request, function( $request ) use ( $layers, $next ) {
			return $this->executeMiddlewareLayers( $layers, $request, $next );
		} );
	}
}
