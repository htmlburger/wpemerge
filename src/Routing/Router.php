<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use Exception;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Facades\Framework;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\HasUrlWhereInterface;
use WPEmerge\Routing\Conditions\InvalidRouteConditionException;
use WPEmerge\Support\Arr;

/**
 * Provide routing for site requests (i.e. all non-api requests)
 */
class Router implements HasRoutesInterface {
	use HasRoutesTrait {
		addRoute as traitAddRoute;
	}

	/**
	 * Current request.
	 *
	 * @var RequestInterface
	 */
	protected $request = null;

	/**
	 * Condition factory.
	 *
	 * @var ConditionFactory
	 */
	protected $condition_factory = null;

	/**
	 * Global middleware.
	 *
	 * @var array
	 */
	protected $middleware = [];

	/**
	 * Global middleware priority.
	 *
	 * @var array
	 */
	protected $middleware_priority = [];

	/**
	 * Default global middleware priority.
	 *
	 * @var integer
	 */
	protected $default_middleware_priority = 0;

	/**
	 * Exception handler.
	 *
	 * @var ErrorHandlerInterface
	 */
	protected $error_handler = null;

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
	 *
	 * @param RequestInterface      $request
	 * @param ConditionFactory      $condition_factory
	 * @param array                 $middleware
	 * @param array                 $middleware_priority
	 * @param integer               $default_middleware_priority
	 * @param ErrorHandlerInterface $error_handler
	 */
	public function __construct(
		RequestInterface $request,
		ConditionFactory $condition_factory,
		$middleware,
		$middleware_priority,
		$default_middleware_priority,
		ErrorHandlerInterface $error_handler
	) {
		$this->request = $request;
		$this->condition_factory = $condition_factory;
		$this->middleware_priority = $middleware_priority;
		$this->default_middleware_priority = $default_middleware_priority;
		$this->middleware = $this->sortMiddleware( $middleware );
		$this->error_handler = $error_handler;
	}

	/**
	 * Hook into WordPress actions.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	public function boot() {
		add_action( 'template_include', [$this, 'execute'], 1000 );
	}

	/**
	 * Get middleware priority.
	 *
	 * @param  mixed   $middleware
	 * @return integer
	 */
	public function getMiddlewarePriority( $middleware ) {
		if ( is_string( $middleware ) && isset( $this->middleware_priority[ $middleware ] ) ) {
			return $this->middleware_priority[ $middleware ];
		}

		return $this->default_middleware_priority;
	}

	/**
	 * Sort middleware by priority in ascending order.
	 *
	 * @param  array $middleware
	 * @return array
	 */
	public function sortMiddleware( $middleware ) {
		usort( $middleware, function ( $a, $b ) use ( $middleware ) {
			$priority = $this->getMiddlewarePriority( $a ) - $this->getMiddlewarePriority( $b );

			if ( $priority !== 0 ) {
				return $priority;
			}

			// Keep original array order.
			return array_search( $a, $middleware ) - array_search( $b, $middleware );
		} );

		return array_values( $middleware );
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
	 * Add a group to the group stack, mergin all previous attributes.
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
			$this->middleware,
			Arr::get( $group, 'middleware', [] ),
			$route->getMiddleware()
		) );

		return $this->traitAddRoute( $route );
	}

	/**
	 * Execute the first satisfied route, if any.
	 *
	 * @internal
	 * @param  string $view
	 * @return string
	 * @throws Exception
	 */
	public function execute( $view ) {
		$routes = $this->getRoutes();

		foreach ( $routes as $route ) {
			if ( $route->isSatisfied( $this->request ) ) {
				$this->setCurrentRoute( $route );
				return $this->handle( $this->request, $route, $view );
			}
		}

		return $view;
	}

	/**
	 * Execute a route.
	 *
	 * @throws Exception
	 * @param  RequestInterface $request
	 * @param  RouteInterface   $route
	 * @param  string           $view
	 * @return string
	 */
	protected function handle( RequestInterface $request, RouteInterface $route, $view ) {
		try {
			$this->error_handler->register();
			$response = $route->handle( $request, $view );
			$this->error_handler->unregister();
		} catch ( Exception $e ) {
			$response = $this->error_handler->getResponse( $e );
		}

		$container = Framework::getContainer();
		$container[ WPEMERGE_RESPONSE_KEY ] = $response;

		return WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php';
	}

	/**
	 * Handle ALL requests.
	 *
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function handleAll( $handler = null ) {
		// match ANY request method
		// match ANY url
		// by default, use built-in WordPress controller
		return $this->any( '*', $handler );
	}
}
