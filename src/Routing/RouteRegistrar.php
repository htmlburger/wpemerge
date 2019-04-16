<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Helpers\Handler;
use WPEmerge\Support\Arr;

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
	 * Default attributes.
	 *
	 * @var array<string, mixed>
	 */
	protected $default_attributes = [];

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
		$this->reset();
	}

	/**
	 * Set the default attributes.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return static               $this
	 */
	public function defaults( $attributes ) {
		$this->default_attributes = $attributes;

		return $this;
	}

	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * Reset attributes to their default values.
	 *
	 * @return static $this
	 */
	public function reset() {
		$this->attributes = $this->default_attributes;

		return $this;
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
	 * Set the namespace attribute.
	 * This should be renamed to namespace for consistency once minimum PHP
	 * version is increased to 7+.
	 *
	 * @param  string $namespace
	 * @return static $this
	 */
	public function controllerNamespace( $namespace ) {
		$this->attributes['namespace'] = $namespace;

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
		$this->reset();
	}

	/**
	 * Create a new route handler.
	 *
	 * @param  string|\Closure|null $handler
	 * @param  string               $namespace
	 * @return Handler
	 */
	protected function makeRouteHandler( $handler, $namespace ) {
		if ( $handler === null ) {
			$handler = Arr::get( $this->attributes, 'controller', null );
		}

		$handler = new Handler( $handler, '', $namespace );

		return $handler;
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

		$this->reset();

		return $route;
	}

	/**
	 * Create and add a new route.
	 *
	 * @codeCoverageIgnore
	 * @param  array<string>        $methods
	 * @param  mixed                $condition
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function route( $methods, $condition, $handler = null ) {
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'route', [$methods, $condition, $handler] );
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
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'get', [$condition, $handler] );
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
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'post', [$condition, $handler] );
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
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'put', [$condition, $handler] );
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
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'patch', [$condition, $handler] );
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
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'delete', [$condition, $handler] );
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
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'options', [$condition, $handler] );
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
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'any', [$condition, $handler] );
	}

	/**
	 * Create and add a route that will always be satisfied.
	 *
	 * @codeCoverageIgnore
	 * @param  string|\Closure|null $handler
	 * @return RouteInterface
	 */
	public function all( $handler = null ) {
		$handler = $this->makeRouteHandler( $handler, Arr::get( $this->attributes, 'namespace', '' ) );
		return $this->makeRoute( 'all', [$handler] );
	}
}
