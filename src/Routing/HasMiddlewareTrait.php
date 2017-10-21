<?php

namespace CarbonFramework\Routing;

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
		$middleware = (array) $middleware;

		foreach ( $middleware as $layer ) {
			if ( ! is_callable( $middleware ) ) {
				throw new Exception( 'Passed middleware must be a callable.' );
			}
		}

		$this->middleware[] = array_merge( (array) $middleware );
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
	 * Execute all registered middleware
	 * 
	 * @param  RequestInterface  $request
	 * @param  callable          $next
	 * @return ResponseInterface
	 */
	public function execute( RequestInterface $request, $next ) {
		$middleware = array_reverse( $this->getMiddleware() ); // last in, first out
		foreach ( $middleware as $layer ) {
			$layer( $request, $next );
		}
	}

	protected function executeLayer( $layer, RequestInterface $request, $next ) {
		
	}
}
