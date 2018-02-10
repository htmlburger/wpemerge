<?php

namespace WPEmerge\Routing;

use Closure;
use Exception;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Requests\Request;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\UrlCondition;

class RouteGroup implements RouteInterface, HasRoutesInterface {
	use HasRoutesTrait {
		route as traitRoute;
	}
	use HasMiddlewareTrait {
		addMiddleware as traitAddMiddleware;
	}

	/**
	 * Route target
	 *
	 * @var ConditionInterface
	 */
	protected $target = null;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 * @param  string|ConditionInterface $target
	 * @param  Closure                   $closure
	 */
	public function __construct( $target, Closure $closure ) {
		if ( is_string( $target ) ) {
			$target = new UrlCondition( $target );
		}

		if ( ! $target instanceof UrlCondition ) {
			throw new Exception( 'Route groups can only use route strings.' );
		}

		$this->target = $target;

		$closure( $this );
	}

	/**
	 * Get the first child route which is satisfied
	 *
	 * @return RouteInterface|null
	 */
	protected function getSatisfiedRoute( Request $request ) {
		$routes = $this->getRoutes();
		foreach ( $routes as $route ) {
			if ( $route->isSatisfied( $request ) ) {
				return $route;
			}
		}
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( Request $request ) {
		$route = $this->getSatisfiedRoute( $request );
		return $route !== null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( Request $request, $view ) {
		$route = $this->getSatisfiedRoute( $request );
		return $route ? $route->handle( $request, $view ) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function route( $methods, $target, $handler ) {
		if ( is_string( $target ) ) {
			$target = new UrlCondition( $target );
		}

		if ( ! $target instanceof UrlCondition ) {
			throw new Exception( 'Routes inside route groups can only use route strings.' );
		}

		$target = $this->target->concatenate( $target );
		return $this->traitRoute( $methods, $target, $handler );
	}

	/**
	 * {@inheritDoc}
	 */
	public function addMiddleware( $middleware ) {
		$routes = $this->getRoutes();

		foreach ( $routes as $route ) {
			$route->addMiddleware( $middleware );
		}

		return $this->traitAddMiddleware( $middleware );
	}
}
