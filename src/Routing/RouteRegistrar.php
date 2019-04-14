<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

/**
 * Provide a fluent interface for registering routes with the router.
 */
class RouteRegistrar {
	/**
	 * Router.
	 *
	 * @var Router
	 */
	protected $router = null;

	/**
	 * Attributes.
	 *
	 * @var array<string, mixed>
	 */
	protected $attributes = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Router $router
	 */
	public function __construct( Router $router ) {
		$this->router = $router;
	}

	/**
	 * Set the condition attribute.
	 *
	 * @param  mixed  $condition
	 * @return static $this
	 */
	public function condition( $condition ) {
		$this->attributes['condition'] = $condition;

		return $this;
	}

	/**
	 * Set a key for the where attribute.
	 *
	 * @param  string $parameter
	 * @param  string $pattern
	 * @return static                $this
	 */
	public function where( $parameter, $pattern ) {
		if ( ! isset( $this->attributes['where'] ) ) {
			$this->attributes['where'] = [];
		}

		$this->attributes['where'][ $parameter ] = $pattern;

		return $this;
	}

	/**
	 * Set the middleware attribute.
	 *
	 * @param  string|array<string> $middleware
	 * @return static               $this
	 */
	public function middleware( $middleware ) {
		$this->attributes['middleware'] = $middleware;

		return $this;
	}

	/**
	 * Create a new route group.
	 *
	 * @param \Closure|string $routes Closure or path to file.
	 * @return void
	 */
	public function group( $routes ) {
		$this->router->group( $this->attributes, $routes );
	}

	/**
	 * Create a new route.
	 *
	 * @param  string         $method
	 * @param  array          $arguments
	 * @return RouteInterface
	 */
	protected function makeRoute( $method, $arguments ) {
		$route = call_user_func_array( [$this->router, $method], $arguments );

		if ( ! empty( $this->attributes['where'] ) ) {
			foreach ( $this->attributes['where'] as $parameter => $pattern ) {
				$route->where( $parameter, $pattern );
			}
		}

		if ( ! empty( $this->attributes['middleware'] ) ) {
			$route->middleware( $this->attributes['middleware'] );
		}

		return $route;
	}

	/**
	 * Create and add a new route.
	 *
	 * @codeCoverageIgnore
	 * @param  string[]             $methods
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $condition, $handler = null ) {
		return $this->makeRoute( 'route', func_get_args() );
	}

	/**
	 * Create and add a route for the GET and HEAD methods.
	 *
	 * @codeCoverageIgnore
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function get( $condition, $handler = null ) {
		return $this->makeRoute( 'get', func_get_args() );
	}

	/**
	 * Create and add a route for the POST method.
	 *
	 * @codeCoverageIgnore
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function post( $condition, $handler = null ) {
		return $this->makeRoute( 'post', func_get_args() );
	}

	/**
	 * Create and add a route for the PUT method.
	 *
	 * @codeCoverageIgnore
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function put( $condition, $handler = null ) {
		return $this->makeRoute( 'put', func_get_args() );
	}

	/**
	 * Create and add a route for the PATCH method.
	 *
	 * @codeCoverageIgnore
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function patch( $condition, $handler = null ) {
		return $this->makeRoute( 'patch', func_get_args() );
	}

	/**
	 * Create and add a route for the DELETE method.
	 *
	 * @codeCoverageIgnore
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function delete( $condition, $handler = null ) {
		return $this->makeRoute( 'delete', func_get_args() );
	}

	/**
	 * Create and add a route for the OPTIONS method.
	 *
	 * @codeCoverageIgnore
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function options( $condition, $handler = null ) {
		return $this->makeRoute( 'options', func_get_args() );
	}

	/**
	 * Create and add a route for all supported methods.
	 *
	 * @codeCoverageIgnore
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function any( $condition, $handler = null ) {
		return $this->makeRoute( 'any', func_get_args() );
	}

	/**
	 * Create and add a route that will always be satisfied.
	 *
	 * @codeCoverageIgnore
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function all( $handler = null ) {
		return $this->makeRoute( 'all', func_get_args() );
	}
}
