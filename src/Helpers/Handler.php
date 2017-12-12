<?php

namespace WPEmerge\Helpers;

use Closure;
use Exception;
use WPEmerge;

/**
 * Represent a generic handler - a Closure or a class method to be resolved from the service container
 */
class Handler {
	/**
	 * Parsed handler
	 *
	 * @var array|Closure|null
	 */
	protected $handler = null;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 * @param  string|Closure $raw_handler
	 */
	public function __construct( $raw_handler ) {
		$handler = $this->parse( $raw_handler );

		if ( $handler === null ) {
			throw new Exception( 'No or invalid handler provided.' );
		}

		$this->handler = $handler;
	}

	/**
	 * Parse a raw handler to a Closure or a [class, method] array
	 *
	 * @param  string|Closure     $handler
	 * @return array|Closure|null
	 */
	protected function parse( $raw_handler ) {
		if ( $raw_handler instanceof Closure ) {
			return $raw_handler;
		}

		if ( is_string( $raw_handler ) )  {
			return $this->parseFromString( $raw_handler );
		}

		return null;
	}

	/**
	 * Parse a raw string handler to a [class, method] array
	 *
	 * @param  string     $handler
	 * @return array|null
	 */
	protected function parseFromString( $raw_handler ) {
		$handlerPieces = preg_split( '/@|::/', $raw_handler, 2 );

		if ( count( $handlerPieces ) === 2 ) {
			return array(
				'class' => $handlerPieces[0],
				'method' => $handlerPieces[1],
			);
		}

		return null;
	}

	/**
	 * Get the parsed handler
	 *
	 * @return array|Closure|null
	 */
	public function get() {
		return $this->handler;
	}

	/**
	 * Execute the parsed handler with any provided arguments and return the result
	 *
	 * @param  mixed $arguments,...
	 * @return mixed
	 */
	public function execute() {
		$arguments = func_get_args();
		if ( is_a( $this->handler, Closure::class ) ) {
			return call_user_func_array( $this->handler, $arguments );
		}

		$class = $this->handler['class'];
		$method = $this->handler['method'];

		$instance = WPEmerge::instantiate( $class );
		return call_user_func_array( [$instance, $method], $arguments );
	}
}
