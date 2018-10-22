<?php

namespace WPEmerge\Routing;

use Closure;
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
	 * @param string|Closure|ConditionInterface $condition
	 * @param Closure|null                      $routes
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
	 * @param  Request $request
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
	 * Merge 2 conditions.
	 * If $parent is a UrlCondition and $child is a UrlCondition, concatenate them.
	 * In all other cases, combine conditions into a MultipleCondition.
	 *
	 * @param  ConditionInterface $parent
	 * @param  ConditionInterface $child
	 * @return ConditionInterface
	 */
	protected function mergeConditions( $parent, $child ) {
		if ( $parent instanceof UrlCondition && $child instanceof UrlCondition ) {
			return $parent->concatenate( $child );
		}

		return new MultipleCondition( [$parent, $child] );
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
	public function getArguments( Request $request ) {
		$route = $this->getSatisfiedRoute( $request );
		return $route ? $route->getArguments( $request ) : [];
	}

	/**
	 * {@inheritDoc}
	 * @throws \WPEmerge\Exceptions\Exception
	 */
	public function route( $methods, $condition, $handler ) {
		if ( ! $condition instanceof ConditionInterface ) {
			$condition = RouteCondition::make( $condition );
		}

		if ( $this->condition !== null ) {
			$condition = $this->mergeConditions( $this->condition, $condition );
		}

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

		if ( $this->condition !== null ) {
			$condition = $this->mergeConditions( $this->condition, $condition );
		}

		return $this->traitGroup( $condition, $routes );
	}

	/**
	 * {@inheritDoc}
	 * @throws \WPEmerge\Exceptions\Exception
	 */
	public function addMiddleware( $middleware ) {
		$routes = $this->getRoutes();

		foreach ( $routes as $route ) {
			$route->addMiddleware( $middleware );
		}

		return $this->traitAddMiddleware( $middleware );
	}
}
