<?php

namespace WPEmerge\Routing;

use Exception;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Facades\Framework;
use WPEmerge\Requests\Request;

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
	 * @var Request
	 */
	protected $request = null;

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
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Request               $request
	 * @param array                 $middleware
	 * @param array                 $middleware_priority
	 * @param integer               $default_middleware_priority
	 * @param ErrorHandlerInterface $error_handler
	 */
	public function __construct(
		Request $request,
		$middleware,
		$middleware_priority,
		$default_middleware_priority,
		ErrorHandlerInterface $error_handler
	) {
		$this->request = $request;
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
		usort( $middleware, function ( $a, $b ) {
			return $this->getMiddlewarePriority( $a ) - $this->getMiddlewarePriority( $b );
		} );

		return array_values( $middleware );
	}

	/**
	 * {@inheritDoc}
	 */
	public function addRoute( $route ) {
		$route->addMiddleware( $this->middleware );
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
	 * @param  Request        $request
	 * @param  RouteInterface $route
	 * @param  string         $view
	 * @return string
	 */
	protected function handle( Request $request, RouteInterface $route, $view ) {
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
