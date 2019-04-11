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
	use HasMiddlewareDefinitionsTrait;
	use SortsMiddlewareTrait;

	/**
	 * Condition factory.
	 *
	 * @var ConditionFactory
	 */
	protected $condition_factory = null;

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

		$attributes = [
			'condition' => $condition !== null ? $condition : '',
			'where' => array_merge(
				Arr::get( $previous, 'where', [] ),
				Arr::get( $attributes, 'where', [] )
			),
			'middleware' => array_merge(
				(array) Arr::get( $previous, 'middleware', [] ),
				(array) Arr::get( $attributes, 'middleware', [] )
			),
		];

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
	 * @param  array             $arguments
	 * @return ResponseInterface
	 */
	protected function handle( RequestInterface $request, RouteInterface $route, $arguments = [] ) {
		$handler = function () use ( $route ) {
			return call_user_func_array( [$route, 'handle'], func_get_args() );
		};

		$middleware = $route->getMiddleware();
		$middleware = $this->expandMiddleware( $middleware );
		$middleware = $this->uniqueMiddleware( $middleware );
		$middleware = $this->sortMiddleware( $middleware );

		$response = ( new Pipeline() )
			->middleware( $middleware )
			->to( $handler )
			->run( $request, [$request, $arguments] );

		return $response;
	}

	/**
	 * Assign and return the first satisfied route (if any) as the current one for the given request.
	 *
	 * @param  RequestInterface $request
	 * @param  array            $arguments
	 * @return RouteInterface
	 */
	public function execute( $request, $arguments = [] ) {
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
