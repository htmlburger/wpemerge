<?php

namespace CarbonFramework\Routing;

use ReflectionClass;
use Exception;
use CarbonFramework\Framework;
use CarbonFramework\Request;
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
	 * Create and return a new condition
	 * 
	 * @param  array              $options
	 * @return ConditionInterface
	 */
	protected function condition( $options ) {
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

	/**
	 * {@inheritDoc}
	 */
	public function satisfied( Request $request ) {
		if ( ! in_array( $request->getMethod(), $this->methods) ) {
			return false;
		}
		return $this->target->satisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( Request $request ) {
		$arguments = array_merge( [$request], $this->target->getArguments( $request ) );
		return $this->executeMiddleware( $this->getMiddleware(), $request, function() use ( $arguments ) {
			return call_user_func_array( [$this->handler, 'execute'], $arguments );
		} );
	}

	/**
	 * Add a rewrite rule to WordPress for url-based routes
	 * 
	 * @param  string $rewrite_to
	 * @return RouteInterface
	 */
	public function rewrite( $rewrite_to ) {
		if ( ! is_a( $this->target, UrlCondition::class ) ) {
			throw new Exception( 'Only routes with url targets can add rewrite rules.' );
		}

		$regex = $this->target->getValidationRegex( $this->target->getUrl(), false );
		$regex = preg_replace( '~^\^/~', '^', $regex ); // rewrite rules require NO leading slash

		add_filter( 'carbon_framework_routing_rewrite_rules', function( $rules ) use ( $regex, $rewrite_to ) {
			$rules[ $regex ] = $rewrite_to;
			return $rules;
		} );

		return $this;
	}
}
