<?php

namespace CarbonFramework\Routing;

use ReflectionClass;
use Exception;
use CarbonFramework\Url;
use CarbonFramework\Framework;
use CarbonFramework\Routing\Conditions\ConditionInterface;
use CarbonFramework\Routing\Conditions\Url as UrlCondition;
use CarbonFramework\Routing\Middleware\HasMiddlewareTrait;

/**
 * Represent a route
 */
class Route implements RouteInterface {
	use HasMiddlewareTrait;

	/**
	 * Allowed methods
	 * 
	 * @var string[]
	 */
	protected $methods = [];

	/**
	 * Route target
	 * 
	 * @var ConditionInterface
	 */
	protected $target = null;

	/**
	 * Route handler
	 * 
	 * @var Handler|null
	 */
	protected $handler = null;

	/**
	 * Constructor
	 * 
	 * @param string[]        $methods
	 * @param mixed           $target
	 * @param string|\Closure $handler
	 */
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

	/**
	 * {@inheritDoc}
	 */
	public function satisfied() {
		if ( ! in_array( $_SERVER['REQUEST_METHOD'], $this->methods) ) {
			return false;
		}
		return $this->target->satisfied();
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( $request ) {
		$arguments = array_merge( [$request], $this->target->getArguments() );
		return $this->executeMiddleware( $this->getMiddleware(), $request, function() use ( $arguments ) {
			return call_user_func_array( [$this->handler, 'execute'], $arguments );
		} );
	}

	/**
	 * Create and return a new condition
	 * 
	 * @param  array $options
	 * @return ConditionInterface
	 */
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
