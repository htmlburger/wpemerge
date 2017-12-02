<?php

namespace Obsidian\Routing;

use Closure;
use Exception;
use Obsidian\Framework;

/**
 * Represent a Closure or a controller method to be executed in response to a request
 */
class Handler {
	/**
	 * Actual handler
	 *
	 * @var array|\Closure|null
	 */
	protected $handler = null;

	/**
	 * Constructor
	 *
	 * @param string|\Closure $handler
	 */
	public function __construct( $handler ) {
		$this->set( $handler );
	}

	/**
	 * Parse a handler to a \Closure or a [class, method] array
	 *
	 * @param  string|\Closure     $handler
	 * @return array|\Closure|null
	 */
	protected function parse( $handler ) {
		if ( $handler instanceof Closure ) {
			return $handler;
		}

		if ( is_string( $handler ) )  {
			return $this->parseFromString( $handler );
		}

		return null;
	}

	/**
	 * Parse a string handler to a [class, method] array
	 *
	 * @param  string              $handler
	 * @return array|\Closure|null
	 */
	protected function parseFromString( $handler ) {
		$handlerPieces = preg_split( '/@|::/', $handler, 2 );

		if ( count( $handlerPieces ) === 2 ) {
			return array(
				'class' => $handlerPieces[0],
				'method' => $handlerPieces[1],
			);
		}

		return null;
	}

	/**
	 * Get the handler
	 *
	 * @return array|\Closure|null
	 */
	public function get() {
		return $this->handler;
	}

	/**
	 * Set the handler
	 *
	 * @param  string|\Closure $new_handler
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
	 * Execute the handler returning raw result
	 *
	 * @return string|array|\Psr\Http\Message\ResponseInterface
	 */
	protected function executeHandler() {
		$arguments = func_get_args();
		if ( is_a( $this->handler, Closure::class ) ) {
			return call_user_func_array( $this->handler, $arguments );
		}

		$class = $this->handler['class'];
		$method = $this->handler['method'];

		$controller = Framework::instantiate( $class );
		return call_user_func_array( [$controller, $method], $arguments );
	}

	/**
	 * Execute the handler
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function execute() {
		$response = call_user_func_array( [$this, 'executeHandler'], func_get_args() );

		if ( is_string( $response ) ) {
			return obs_output( $response );
		}

		if ( is_array( $response ) ) {
			return obs_json( $response );
		}

		return $response;
	}
}
