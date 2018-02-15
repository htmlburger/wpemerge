<?php

namespace WPEmerge\Routing;

use Closure;
use Exception;
use WPEmerge\Facades\RouteCondition;
use WPEmerge\Helpers\Arguments;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Requests\Request;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\UrlCondition;
use WPEmerge\Routing\Conditions\MultipleCondition;

class RouteGroup implements RouteInterface, HasRoutesInterface {
	use HasRoutesTrait {
		route as traitRoute;
		group as traitGroup;
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
	 * @param  string|Closure $condition
	 * @param  Closure|null   $routes
	 */
	public function __construct( $condition, $routes = null ) {
		list( $condition, $routes ) = Arguments::flip( $condition, $routes );

		if ( $condition !== null && ! $condition instanceof ConditionInterface ) {
			$condition = RouteCondition::make( $condition );
		}

		$this->condition = $condition;

		$routes( $this );
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
	 * Merge 2 conditions (in supplied order).
	 *
	 * @param  ConditionInterface|null $parent
	 * @param  ConditionInterface      $child
	 * @return ConditionInterface
	 */
	protected function mergeConditions( $parent, $child ) {
		if ( $parent === null ) {
			return $child;
		}

		if ( $parent instanceof UrlCondition ) {
			if ( $child instanceof UrlCondition ) {
				return $parent->concatenate( $child );
			}

			// Ignore parent if conditions are incompatible.
			return $child;
		}

		return new MultipleCondition( [ $parent, $child ] );
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
		if ( ! $condition instanceof ConditionInterface ) {
			$condition = RouteCondition::make( $condition );
		}

		$condition = $this->mergeConditions( $this->condition, $condition );

		return $this->traitRoute( $methods, $condition, $handler );
	}

	/**
	 * {@inheritDoc}
	 */
	public function group( $condition, $routes = null ) {
		list( $condition, $routes ) = Arguments::flip( $condition, $routes );

		if ( $condition !== null && ! $condition instanceof ConditionInterface ) {
			$condition = RouteCondition::make( $condition );
		}

		$condition = $this->mergeConditions( $this->condition, $condition );

		return $this->traitGroup( $condition, $routes );
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
