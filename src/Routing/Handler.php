<?php

namespace CarbonFramework\Routing;

use Closure;
use Exception;
use CarbonFramework\Framework;

/**
 * Represent a closure or a controller method to be executed in response to a request
 */
class Handler {
	/**
	 * Actual handler
	 * 
	 * @var string|array|Closure|null
	 */
	protected $handler = null;

	/**
	 * Constructor
	 * 
	 * @param string|Closure $handler
	 */
	public function __construct( $handler ) {
		$this->set( $handler );
	}

	/**
	 * Parse a handler to a callable or a [class, method] array
	 * 
	 * @param  string|Closure      $handler
	 * @return callable|array|null
	 */
	protected function parse( $handler ) {
		if ( $handler instanceof Closure ) {
			return $handler;
		}

		if ( is_string( $handler ) )  {
			$handlerPieces = preg_split( '/@|::/', $handler, 2 );
			if ( count( $handlerPieces ) === 1 ) {
				if ( is_callable( $handlerPieces[0] ) ) {
					return $handlerPieces[0];
				}
				return null;
			}
			return array(
				'class' => $handlerPieces[0],
				'method' => $handlerPieces[1],
			);
		}

		return null;
	}

	/**
	 * Set the handler
	 * 
	 * @param  string|Closure $new_handler
	 * @return null
	 */
	public function set( $new_handler ) {
		$handler = $this->parse( $new_handler );

		if ( $handler === null ) {
			throw new Exception( 'No or invalid handler provided.' );
		}

		$this->handler = $handler;
	}

	/**
	 * Execute the handler
	 * 
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function execute() {
		$arguments = func_get_args();
		if ( is_callable( $this->handler ) ) {
			return call_user_func_array( $this->handler, $arguments );
		}

		$class = $this->handler['class'];
		$method = $this->handler['method'];

		$controller = Framework::instantiate( $class );
		return call_user_func_array( [$controller, $method], $arguments );
	}
}
