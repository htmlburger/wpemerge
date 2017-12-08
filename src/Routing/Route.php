<?php

namespace WPEmerge\Routing;

use Exception;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Request;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\Factory;
use WPEmerge\Routing\Conditions\InvalidRouteConditionException;
use WPEmerge\Routing\Conditions\Url as UrlCondition;

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
	 * @var Handler
	 */
	protected $handler = null;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 * @param  string[]        $methods
	 * @param  mixed           $target
	 * @param  string|\Closure $handler
	 */
	public function __construct( $methods, $target, $handler ) {
		if ( ! is_a( $target, ConditionInterface::class ) ) {
			try {
				$target = Factory::make( $target );
			} catch ( InvalidRouteConditionException $e ) {
				throw new Exception( 'Route target is not a valid route string or condition.' );
			}
		}

		$this->methods = $methods;
		$this->target = $target;
		$this->handler = new Handler( $handler );
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
	 * Get target
	 *
	 * @return ConditionInterface
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * Get handler
	 *
	 * @return Handler
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
		return $this->target->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( Request $request, $view ) {
		$arguments = array_merge( [$request, $view], $this->target->getArguments( $request ) );
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
		if ( ! is_a( $this->target, UrlCondition::class ) ) {
			throw new Exception( 'Only routes with url targets can add rewrite rules.' );
		}

		$regex = $this->target->getValidationRegex( $this->target->getUrl(), false );
		$regex = preg_replace( '~^\^/~', '^', $regex ); // rewrite rules require NO leading slash

		add_filter( 'wpemerge.routing.rewrite_rules', function( $rules ) use ( $regex, $rewrite_to ) {
			$rules[ $regex ] = $rewrite_to;
			return $rules;
		} );

		return $this;
	}
}
