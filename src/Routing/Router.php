<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use Psr\Http\Message\ResponseInterface;
use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\HasUrlWhereInterface;
use WPEmerge\Routing\Conditions\InvalidRouteConditionException;
use WPEmerge\Support\Arr;

/**
 * Provide routing for site requests (i.e. all non-api requests).
 */
class Router implements HasRoutesInterface {
	use HasRoutesTrait {
		addRoute as traitAddRoute;
	}

	/**
	 * Condition factory.
	 *
	 * @var ConditionFactory
	 */
	protected $condition_factory = null;

	/**
	 * Middleware available to the application.
	 *
	 * @var array
	 */
	protected $middleware = [];

	/**
	 * Middleware groups.
	 *
	 * @var array<string, array>
	 */
	protected $middleware_groups = [];

	/**
	 * Global middleware that will be applied to all routes.
	 *
	 * @var array
	 */
	protected $global_middleware = [];

	/**
	 * Middleware sorted in order of execution.
	 *
	 * @var array<string>
	 */
	protected $middleware_priority = [];

	/**
	 * Current active route.
	 *
	 * @var RouteInterface
	 */
	protected $current_route = null;

	/**
	 * Group stack.
	 *
	 * @var array<array<string, mixed>>
	 */
	protected $group_stack = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ConditionFactory      $condition_factory
	 */
	public function __construct(
		ConditionFactory $condition_factory
	) {
		$this->condition_factory = $condition_factory;
	}

	/**
	 * Register middleware.
	 *
	 * @codeCoverageIgnore
	 * @param  array $middleware
	 * @return void
	 */
	public function setMiddleware( $middleware ) {
		$this->middleware = $middleware;
	}

	/**
	 * Register middleware groups.
	 *
	 * @codeCoverageIgnore
	 * @param  array $middleware_groups
	 * @return void
	 */
	public function setMiddlewareGroups( $middleware_groups ) {
		$this->middleware_groups = $middleware_groups;
	}

	/**
	 * Register global middleware.
	 *
	 * @codeCoverageIgnore
	 * @param  array $middleware
	 * @return void
	 */
	public function setGlobalMiddleware( $middleware ) {
		$this->global_middleware = $middleware;
	}

	/**
	 * Register middleware execution priority.
	 *
	 * @codeCoverageIgnore
	 * @param  array $middleware_priority
	 * @return void
	 */
	public function setMiddlewarePriority( $middleware_priority ) {
		$this->middleware_priority = $middleware_priority;
	}

	/**
	 * Get middleware priority.
	 * This is in reverse compared to definition order.
	 * Middleware with unspecified priority will yield -1.
	 *
	 * @internal
	 * @param  mixed   $middleware
	 * @return integer
	 */
	public function getMiddlewarePriority( $middleware ) {
		$increasing_priority = array_reverse( $this->middleware_priority );
		$priority = array_search( $middleware, $increasing_priority );
		return $priority !== false ? (int) $priority : -1;
	}

	/**
	 * Sort array of fully qualified middleware class names by priority in ascending order.
	 *
	 * @internal
	 * @param  array $middleware
	 * @return array
	 */
	public function sortMiddleware( $middleware ) {
		$sorted = $middleware;

		usort( $sorted, function ( $a, $b ) use ( $middleware ) {
			$priority = $this->getMiddlewarePriority( $b ) - $this->getMiddlewarePriority( $a );

			if ( $priority !== 0 ) {
				return $priority;
			}

			// Keep relative order from original array.
			return array_search( $a, $middleware ) - array_search( $b, $middleware );
		} );

		return array_values( $sorted );
	}

	/**
	 * Expand array of middleware into an array of fully qualified class names.
	 *
	 * @internal
	 * @param  array<string> $middleware
	 * @return array<string>
	 */
	public function expandMiddleware( $middleware ) {
		$classes = [];

		foreach ( $middleware as $item ) {
			if ( isset( $this->middleware_groups[ $item ] ) ) {
				$classes = array_merge(
					$classes,
					$this->expandMiddlewareGroup( $item )
				);
				continue;
			}

			$classes[] = $this->expandMiddlewareItem( $item );
		}

		return $classes;
	}

	/**
	 * Expand a middleware group into an array of fully qualified class names.
	 *
	 * @internal
	 * @param  string        $group
	 * @return array<string>
	 */
	public function expandMiddlewareGroup( $group ) {
		if ( ! isset( $this->middleware_groups[ $group ] ) ) {
			throw new ConfigurationException( 'Unknown middleware group "' . $group . '" used.' );
		}

		return array_map( [$this, 'expandMiddlewareItem'], $this->middleware_groups[ $group ] );
	}

	/**
	 * Expand a middleware into a fully qualified class name.
	 *
	 * @internal
	 * @param  string $middleware
	 * @return string
	 */
	public function expandMiddlewareItem( $middleware ) {
		if ( is_subclass_of( $middleware, MiddlewareInterface::class ) ) {
			return $middleware;
		}

		if ( ! isset( $this->middleware[ $middleware ] ) ) {
			throw new ConfigurationException( 'Unknown middleware "' . $middleware . '" used.' );
		}

		return $this->middleware[ $middleware ];
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
	 * Add a group to the group stack, merging all previous attributes.
	 *
	 * @param array<string, mixed> $attributes
	 * @return void
	 */
	protected function addGroupToStack( $attributes ) {
		$previous = Arr::last( $this->group_stack, null, [] );

		$condition = $this->condition_factory->merge(
			Arr::get( $previous, 'condition', '' ),
			Arr::get( $attributes, 'condition', '' )
		);

		$attributes = array(
			'condition' => $condition !== null ? $condition : '',
			'where' => array_merge(
				Arr::get( $previous, 'where', [] ),
				Arr::get( $attributes, 'where', [] )
			),
			'middleware' => array_merge(
				(array) Arr::get( $previous, 'middleware', [] ),
				(array) Arr::get( $attributes, 'middleware', [] )
			),
		);

		$this->group_stack[] = $attributes;
	}

	/**
	 * Remove last group from the group stack.
	 *
	 * @return void
	 */
	protected function removeLastGroupFromStack() {
		array_pop( $this->group_stack );
	}

	/**
	 * Create a new route group.
	 *
	 * @param array<string, mixed> $attributes
	 * @param \Closure            $routes
	 * @return void
	 */
	public function group( $attributes, $routes ) {
		$this->addGroupToStack( $attributes );

		$routes();

		$this->removeLastGroupFromStack();
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeRoute( $methods, $condition, $handler ) {
		if ( ! $condition instanceof ConditionInterface ) {
			try {
				$condition = $this->condition_factory->make( $condition );
			} catch ( InvalidRouteConditionException $e ) {
				throw new InvalidRouteConditionException( 'Route condition is not a valid route string or condition.' );
			}
		}

		return new Route( $methods, $condition, $handler );
	}

	/**
	 * {@inheritDoc}
	 */
	public function addRoute( $route ) {
		$group = Arr::last( $this->group_stack, null, [] );
		$condition = $route->getCondition();

		if ( $condition instanceof HasUrlWhereInterface ) {
			$condition->setUrlWhere( array_merge(
				Arr::get( $group, 'where', [] ),
				$condition->getUrlWhere()
			) );
		}

		$condition = $this->condition_factory->merge(
			Arr::get( $group, 'condition', '' ),
			$condition
		);

		$route->setCondition( $condition );

		$route->setMiddleware( array_merge(
			Arr::get( $group, 'middleware', [] ),
			$route->getMiddleware()
		) );

		return $this->traitAddRoute( $route );
	}

	/**
	 * Handle ALL requests.
	 *
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function handleAll( $handler = null ) {
		// Match ANY request method.
		// Match ANY url.
		// By default, use built-in WordPress controller.
		return $this->any( '*', $handler );
	}

	/**
	 * Execute a route.
	 *
	 * @param  RequestInterface  $request
	 * @param  RouteInterface    $route
	 * @param  string            $view
	 * @return ResponseInterface
	 */
	protected function handle( RequestInterface $request, RouteInterface $route, $view ) {
		$handler = function ( $request, $view ) use ( $route ) {
			return $route->handle( $request, $view );
		};

		$global_middleware = $this->expandMiddleware( $this->global_middleware );
		$route_middleware = $this->sortMiddleware( $this->expandMiddleware( $route->getMiddleware() ) );

		$response = ( new Pipeline() )
			->middleware( $global_middleware )
			->middleware( $route_middleware )
			->to( $handler )
			->run( $request, [$request, $view] );

		return $response;
	}

	/**
	 * Execute the first satisfied route, if any.
	 *
	 * @param  RequestInterface       $request
	 * @param  string                 $view
	 * @return ResponseInterface|null
	 */
	public function execute( $request, $view ) {
		$routes = $this->getRoutes();

		foreach ( $routes as $route ) {
			if ( $route->isSatisfied( $request ) ) {
				$this->setCurrentRoute( $route );
				return $this->handle( $request, $route, $view );
			}
		}

		return null;
	}
}
