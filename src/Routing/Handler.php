<?php

namespace WPEmerge\Routing;

use WPEmerge;
use WPEmerge\Helpers\Handler as GenericHandler;
use WPEmerge\Responses\ConvertibleToResponseInterface;

/**
 * Represent a Closure or a controller method to be executed in response to a request
 */
class Handler {
	/**
	 * Actual handler
	 *
	 * @var GenericHandler
	 */
	protected $handler = null;

	/**
	 * Constructor
	 *
	 * @param string|\Closure $handler
	 */
	public function __construct( $handler ) {
		$this->handler = new GenericHandler( $handler );
	}

	/**
	 * Get the handler
	 *
	 * @return GenericHandler
	 */
	public function get() {
		return $this->handler;
	}

	/**
	 * Execute the handler
	 *
	 * @param  mixed $arguments,...
	 * @return mixed
	 */
	public function execute() {
		$arguments = func_get_args();
		$response = call_user_func_array( [$this->handler, 'execute'], $arguments );

		if ( is_string( $response ) ) {
			return WPEmerge\output( $response );
		}

		if ( is_array( $response ) ) {
			return WPEmerge\json( $response );
		}

		if ( $response instanceof ConvertibleToResponseInterface ) {
			return $response->toResponse();
		}

		return $response;
	}
}
