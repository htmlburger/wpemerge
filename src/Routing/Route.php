<?php

namespace WPEmerge\Routing;

use Exception;
use WPEmerge\Facades\RouteCondition;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Requests\Request;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\InvalidRouteConditionException;
use WPEmerge\Routing\Conditions\UrlCondition;

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
	 * Route condition
	 *
	 * @var ConditionInterface
	 */
	protected $condition = null;

	/**
	 * Route handler
	 *
	 * @var RouteHandler
	 */
	protected $handler = null;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 * @param  string[]        $methods
	 * @param  mixed           $condition
	 * @param  string|\Closure $handler
	 */
	public function __construct( $methods, $condition, $handler ) {
		if ( ! $condition instanceof ConditionInterface ) {
			try {
				$condition = RouteCondition::make( $condition );
			} catch ( InvalidRouteConditionException $e ) {
				throw new Exception( 'Route condition is not a valid route string or condition.' );
			}
		}

		$this->methods = $methods;
		$this->condition = $condition;
		$this->handler = new RouteHandler( $handler );
	}

	/**
	 * Get allowed methods
	 *
	 * @return string[]
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * Get condition
	 *
	 * @return ConditionInterface
	 */
	public function getCondition() {
		return $this->condition;
	}

	/**
	 * Get handler
	 *
	 * @return RouteHandler
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		if ( ! in_array( $request->getMethod(), $this->methods) ) {
			return false;
		}
		return $this->condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( Request $request, $view ) {
		$arguments = array_merge( [$request, $view], $this->condition->getArguments( $request ) );
		return $this->executeMiddleware( $this->getMiddleware(), $request, function() use ( $arguments ) {
			return call_user_func_array( [$this->handler, 'execute'], $arguments );
		} );
	}

	/**
	 * Add a rewrite rule to WordPress for url-based routes
	 *
	 * @throws Exception
	 * @param  string    $rewrite_to
	 * @return static    $this
	 */
	public function rewrite( $rewrite_to ) {
		if ( ! $this->condition instanceof UrlCondition ) {
			throw new Exception( 'Only routes with url conditions can add rewrite rules.' );
		}

		$regex = $this->condition->getValidationRegex( $this->condition->getUrl(), false );
		$regex = preg_replace( '~^\^/~', '^', $regex ); // rewrite rules require NO leading slash

		add_filter( 'wpemerge.routing.rewrite_rules', function( $rules ) use ( $regex, $rewrite_to ) {
			$rules[ $regex ] = $rewrite_to;
			return $rules;
		} );

		return $this;
	}
}
