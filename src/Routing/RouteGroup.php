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
	 * Route condition
	 *
	 * @var ConditionInterface
	 */
	protected $condition = null;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 * @param  string|ConditionInterface $condition
	 * @param  Closure                   $closure
	 */
	public function __construct( $condition, Closure $closure ) {
		if ( is_string( $condition ) ) {
			$condition = new UrlCondition( $condition );
		}

		if ( ! $condition instanceof UrlCondition ) {
			throw new Exception( 'Route groups can only use route strings.' );
		}

		$this->condition = $condition;

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
	public function route( $methods, $condition, $handler ) {
		if ( is_string( $condition ) ) {
			$condition = new UrlCondition( $condition );
		}

		if ( ! $condition instanceof UrlCondition ) {
			throw new Exception( 'Routes inside route groups can only use route strings.' );
		}

		$condition = $this->condition->concatenate( $condition );
		return $this->traitRoute( $methods, $condition, $handler );
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
