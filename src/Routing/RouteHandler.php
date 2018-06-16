<?php

namespace WPEmerge\Routing;

use Exception;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Facades\Response;
use WPEmerge\Helpers\Handler;
use WPEmerge\Responses\ResponsableInterface;

/**
 * Represent a Closure or a controller method to be executed in response to a request
 */
class RouteHandler {
	/**
	 * Actual handler
	 *
	 * @var Handler
	 */
	protected $handler = null;

	/**
	 * Constructor
	 *
	 * @param string|\Closure $handler
	 */
	public function __construct( $handler ) {
		$this->handler = new Handler( $handler );
	}

	/**
	 * Get the handler
	 *
	 * @return Handler
	 */
	public function get() {
		return $this->handler;
	}

	/**
	 * Convert a user returned response to a ResponseInterface instance if possible.
	 * Return the original value if unsupported.
	 *
	 * @param  mixed $response
	 * @return mixed
	 */
	protected function getResponse( $response ) {
		if ( is_string( $response ) ) {
			return Response::output( $response );
		}

		if ( is_array( $response ) ) {
			return Response::json( $response );
		}

		if ( $response instanceof ResponsableInterface ) {
			return $response->toResponse();
		}

		return $response;
	}

	/**
	 * Execute the handler
	 *
	 * @throws Exception
	 * @param  mixed             $arguments,...
	 * @return ResponseInterface
	 */
	public function execute() {
		$response = call_user_func_array( [$this->handler, 'execute'], func_get_args() );
		$response = $this->getResponse( $response );

		if ( ! $response instanceof ResponseInterface ) {
			throw new Exception( 'Response returned by controller is not valid (expected ' . ResponseInterface::class . '; received ' . gettype( $response ) . ').' );
		}

		return $response;
	}
}
