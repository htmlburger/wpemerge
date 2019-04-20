<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Helpers\Handler;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Support\Arr;

/**
 * Provide routing for site requests (i.e. all non-api requests).
 */
class Router implements HasRoutesInterface {
	use HasRoutesTrait;

	/**
	 * Condition factory.
	 *
	 * @var ConditionFactory
	 */
	protected $condition_factory = null;

	/**
	 * Group stack.
	 *
	 * @var array<array<string, mixed>>
	 */
	protected $group_stack = [];

	/**
	 * Current active route.
	 *
	 * @var RouteInterface
	 */
	protected $current_route = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ConditionFactory $condition_factory
	 */
	public function __construct( ConditionFactory $condition_factory ) {
		$this->condition_factory = $condition_factory;
	}

	/**
	 * Get the current route.
	 *
	 * @return RouteInterface
	 */
	public function getCurrentRoute() {
		return $this->current_route;
	}

	/**
	 * Set the current route.
	 *
	 * @param  RouteInterface
	 * @return void
	 */
	public function setCurrentRoute( RouteInterface $current_route ) {
		$this->current_route = $current_route;
	}

	/**
	 * Merge the methods attribute combining values.
	 *
	 * @param  array<string> $old
	 * @param  array<string> $new
	 * @return array<string>
	 */
	public function mergeMethodsAttribute( $old, $new ) {
		return array_merge( $old, $new );
	}

	/**
	 * Merge the condition attribute.
	 *
	 * @param  string|array|\Closure|ConditionInterface $old
	 * @param  string|array|\Closure|ConditionInterface $new
	 * @return ConditionInterface|string
	 */
	public function mergeConditionAttribute( $old, $new ) {
		try {
			$condition = $this->condition_factory->merge( $old, $new );
		} catch ( ConfigurationException $e ) {
			throw new ConfigurationException( 'Route condition is not a valid route string or condition.' );
		}

		return $condition !== null ? $condition : '';
	}

	/**
	 * Merge the middleware attribute combining values.
	 *
	 * @param  array<string> $old
	 * @param  array<string> $new
	 * @return array<string>
	 */
	public function mergeMiddlewareAttribute( $old, $new ) {
		return array_merge( $old, $new );
	}

	/**
	 * Merge the namespace attribute taking the latest value.
	 *
	 * @param  string $old
	 * @param  string $new
	 * @return string
	 */
	public function mergeNamespaceAttribute( $old, $new ) {
		return ! empty( $new ) ? $new : $old;
	}

	/**
	 * Merge the handler attribute taking the latest value.
	 *
	 * @param  string|\Closure $old
	 * @param  string|\Closure $new
	 * @return string|\Closure
	 */
	public function mergeHandlerAttribute( $old, $new ) {
		return ! empty( $new ) ? $new : $old;
	}

	/**
	 * Merge attributes into route.
	 *
	 * @param  array<string, mixed> $old
	 * @param  array<string, mixed> $new
	 * @return array<string, mixed>
	 */
	public function mergeAttributes( $old, $new ) {
		$attributes = [
			'methods' => $this->mergeMethodsAttribute(
				(array) Arr::get( $old, 'methods', [] ),
				(array) Arr::get( $new, 'methods', [] )
			),

			'condition' => $this->mergeConditionAttribute(
				Arr::get( $old, 'condition', '' ),
				Arr::get( $new, 'condition', '' )
			),

			'middleware' => $this->mergeMiddlewareAttribute(
				(array) Arr::get( $old, 'middleware', [] ),
				(array) Arr::get( $new, 'middleware', [] )
			),

			'namespace' => $this->mergeNamespaceAttribute(
				Arr::get( $old, 'namespace', '' ),
				Arr::get( $new, 'namespace', '' )
			),

			'handler' => $this->mergeNamespaceAttribute(
				Arr::get( $old, 'handler', '' ),
				Arr::get( $new, 'handler', '' )
			),
		];

		return $attributes;
	}

	/**
	 * Get the top group from the stack.
	 *
	 * @codeCoverageIgnore
	 * @return array<string, mixed>
	 */
	protected function getGroup() {
		return Arr::last( $this->group_stack, null, [] );
	}

	/**
	 * Add a group to the group stack, merging all previous attributes.
	 *
	 * @codeCoverageIgnore
	 * @param array<string, mixed> $group
	 * @return void
	 */
	protected function pushGroup( $group ) {
		$this->group_stack[] = $this->mergeAttributes( $this->getGroup(), $group );
	}

	/**
	 * Remove last group from the group stack.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	protected function popGroup() {
		array_pop( $this->group_stack );
	}

	/**
	 * Create a route group.
	 *
	 * @codeCoverageIgnore
	 * @param array<string, mixed> $attributes
	 * @param \Closure|string      $routes Closure or path to file.
	 * @return void
	 */
	public function group( $attributes, $routes ) {
		$this->pushGroup( $attributes );

		if ( is_string( $routes ) ) {
			// @codeCoverageIgnore
			require_once $routes;
		} else {
			$routes();
		}

		$this->popGroup();
	}

	/**
	 * Make a route condition.
	 *
	 * @param  mixed              $condition
	 * @return ConditionInterface
	 */
	protected function routeCondition( $condition ) {
		if ( $condition === '' ) {
			throw new ConfigurationException( 'No route condition specified. Did you miss to call url() or where()?' );
		}

		if ( ! $condition instanceof ConditionInterface ) {
			$condition = $this->condition_factory->make( $condition );
		}

		return $condition;
	}

	/**
	 * Make a route handler.
	 *
	 * @codeCoverageIgnore
	 * @param  string|\Closure|null $handler
	 * @param  string               $namespace
	 * @return Handler
	 */
	protected function routeHandler( $handler, $namespace ) {
		return new Handler( $handler, '', $namespace );
	}

	/**
	 * Make a route.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return RouteInterface
	 */
	public function route( $attributes ) {
		$attributes = $this->mergeAttributes( $this->getGroup(), $attributes );

		$methods = Arr::get( $attributes, 'methods', [] );
		$condition = Arr::get( $attributes, 'condition', '' );
		$handler = Arr::get( $attributes, 'handler', '' );
		$namespace = Arr::get( $attributes, 'namespace', '' );
		$middleware = Arr::get( $attributes, 'middleware', [] );

		$condition = $this->routeCondition( $condition );
		$handler = $this->routeHandler( $handler, $namespace );

		$route = new Route( $methods, $condition, $handler );

		$route->middleware( $middleware );

		return $route;
	}

	/**
	 * Assign and return the first satisfied route (if any) as the current one for the given request.
	 *
	 * @param  RequestInterface $request
	 * @return RouteInterface
	 */
	public function execute( $request ) {
		$routes = $this->getRoutes();

		foreach ( $routes as $route ) {
			if ( $route->isSatisfied( $request ) ) {
				$this->setCurrentRoute( $route );
				return $route;
			}
		}

		return null;
	}
}
