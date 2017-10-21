<?php

namespace CarbonFramework\Routing;

use ReflectionClass;
use Exception;
use CarbonFramework\Url;
use CarbonFramework\Framework;
use CarbonFramework\Routing\Conditions\ConditionInterface;
use CarbonFramework\Routing\Conditions\Url as UrlCondition;
use CarbonFramework\Routing\Middleware\HasMiddlewareTrait;

class Route implements RouteInterface {
	use HasMiddlewareTrait;

	protected $methods = [];

	protected $target = null;

	protected $handler = null;

	public function __construct( $methods, $target, $handler ) {
		if ( is_string( $target ) ) {
			$target = new UrlCondition( $target );
		}

		if ( is_array( $target ) ) {
			$target = $this->condition( $target );
		}

		if ( ! is_a( $target, ConditionInterface::class ) ) {
			throw new Exception( 'Route target is not a valid route string or condition.' );
		}

		$this->methods = $methods;
		$this->target = $target;
		$this->handler = new Handler( $handler );
	}

	public function satisfied() {
		if ( ! in_array( $_SERVER['REQUEST_METHOD'], $this->methods) ) {
			return false;
		}
		return $this->target->satisfied();
	}

	public function handle( $request ) {
		$arguments = array_merge( [$request], $this->target->getArguments() );
		return $this->executeMiddleware( $this->getMiddleware(), $request, function() use ( $arguments ) {
			return call_user_func_array( [$this->handler, 'execute'], $arguments );
		} );
	}

	public function condition( $options ) {
		if ( count( $options ) === 0 ) {
			throw new Exception( 'No condition type specified.' );
		}

		$condition_type = $options[0];
		$arguments = array_slice( $options, 1 );

		$condition_class = Framework::resolve( 'framework.routing.conditions.' . $condition_type );
		if ( $condition_class === null ) {
			throw new Exception( 'Unknown condition type specified: ' . $condition_type );
		}

		$reflection = new ReflectionClass( $condition_class );
		$condition = $reflection->newInstanceArgs( $arguments );
		return $condition;
	}
}
