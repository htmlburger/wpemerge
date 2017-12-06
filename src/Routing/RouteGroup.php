<?php

namespace Obsidian\Routing;

use Closure;
use Exception;
use Obsidian\Middleware\HasMiddlewareTrait;
use Obsidian\Request;
use Obsidian\Routing\Conditions\ConditionInterface;
use Obsidian\Routing\Conditions\Url as UrlCondition;

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

		if ( ! is_a( $target, UrlCondition::class ) ) {
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
	public function handle( Request $request, $template ) {
		$route = $this->getSatisfiedRoute( $request );
		return $route ? $route->handle( $request, $template ) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function route( $methods, $target, $handler ) {
		if ( is_string( $target ) ) {
			$target = new UrlCondition( $target );
		}

		if ( ! is_a( $target, UrlCondition::class ) ) {
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
