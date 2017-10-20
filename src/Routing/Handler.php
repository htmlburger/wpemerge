<?php

namespace CarbonFramework\Routing;

use Closure;
use Exception;
use CarbonFramework\Framework;

class Handler {
	protected $handler = null;

	public function __construct( $handler ) {
		$this->set( $handler );
	}

	protected function parse( $handler ) {
		if ( $handler instanceof Closure ) {
			return $handler;
		}

		if ( is_string( $handler ) )  {
			$handlerPieces = preg_split( '/@|::/', $handler, 2 );
			if ( count( $handlerPieces ) === 1 ) {
				if ( is_callable( $handlerPieces ) ) {
					return $handlerPieces[0];
				} else {
					return null;
				}
			} else {
				return array(
					'class' => $handlerPieces[0],
					'method' => $handlerPieces[1],
				);
			}
		}

		return null;
	}

	public function set( $new_handler ) {
		$handler = $this->parse( $new_handler );

		if ( $handler === null ) {
			throw new Exception( 'No or invalid handler provided.' );
		}

		$this->handler = $handler;
	}

	public function execute() {
		$arguments = func_get_args();
		if ( ! is_array( $this->handler ) ) {
			return call_user_func_array( $this->handler, $arguments );
		}

		$class = $this->handler['class'];
		$method = $this->handler['method'];

		$controller = Framework::instantiate( $class );
		return call_user_func_array( [$controller, $method], $arguments );
	}
}
