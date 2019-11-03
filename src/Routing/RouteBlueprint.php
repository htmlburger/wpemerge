<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use Closure;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Support\Arr;
use WPEmerge\View\ViewService;

/**
 * Provide a fluent interface for registering routes with the router.
 */
class RouteBlueprint {
	/**
	 * Router.
	 *
	 * @var Router
	 */
	protected $router = null;

	/**
	 * View service.
	 *
	 * @var ViewService
	 */
	protected $view_service = null;

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
	 * @param Router      $router
	 * @param ViewService $view_service
	 */
	public function __construct( Router $router, ViewService $view_service ) {
		$this->router = $router;
		$this->view_service = $view_service;
	}

	/**
	 * Get attributes.
	 *
	 * @return array<string, mixed>
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * Set the attributes.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return void
	 */
	public function setAttributes( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Fluent alias for setAttributes().
	 *
	 * @codeCoverageIgnore
	 * @param  array<string, mixed> $attributes
	 * @return static               $this
	 */
	public function attributes( $attributes ) {
		$this->setAttributes( $attributes );

		return $this;
	}

	/**
	 * Get attribute.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getAttribute( $key, $default = '' ) {
		return Arr::get( $this->getAttributes(), $key, $default );
	}

	/**
	 * Set attribute.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function setAttribute( $key, $value ) {
		$this->setAttributes( array_merge(
			$this->getAttributes(),
			[$key => $value]
		) );
	}

	/**
	 * Set attribute.
	 *
	 * @codeCoverageIgnore
	 * @param  string $key
	 * @param  mixed  $value
	 * @return static $this
	 */
	public function attribute( $key, $value ) {
		$this->setAttribute( $key, $value );

		return $this;
	}

	/**
	 * Match requests using one of the specified methods.
	 *
	 * @param  array<string> $methods
	 * @return static        $this
	 */
	public function methods( $methods ) {
		$methods = array_merge(
			$this->getAttribute( 'methods', [] ),
			$methods
		);

		return $this->attribute( 'methods', $methods );
	}

	/**
	 * Set the condition attribute to a URL.
	 *
	 * @param  string                $url
	 * @param  array<string, string> $where
	 * @return static                $this
	 */
	public function url( $url, $where = [] ) {
		return $this->where( 'url', $url, $where );
	}

	/**
	 * Set the condition attribute.
	 *
	 * @param  string|array|ConditionInterface $condition
	 * @param  mixed                           ,...$arguments
	 * @return static                          $this
	 */
	public function where( $condition ) {
		if ( ! $condition instanceof ConditionInterface ) {
			$condition = func_get_args();
		}

		$condition = $this->router->mergeConditionAttribute(
			$this->getAttribute( 'condition' ),
			$condition
		);

		return $this->attribute( 'condition', $condition );
	}

	/**
	 * Set the middleware attribute.
	 *
	 * @param  string|array<string> $middleware
	 * @return static               $this
	 */
	public function middleware( $middleware ) {
		$middleware = array_merge(
			(array) $this->getAttribute( 'middleware', [] ),
			(array) $middleware
		);

		return $this->attribute( 'middleware', $middleware );
	}

	/**
	 * Set the namespace attribute.
	 * This should be renamed to namespace for consistency once minimum PHP
	 * version is increased to 7+.
	 *
	 * @param  string $namespace
	 * @return static $this
	 */
	public function setNamespace( $namespace ) {
		return $this->attribute( 'namespace', $namespace );
	}

	/**
	 * Set the query attribute.
	 *
	 * @param  callable $query
	 * @return static   $this
	 */
	public function query( $query ) {
		$query = $this->router->mergeQueryAttribute(
			$this->getAttribute( 'query', null ),
			$query
		);

		return $this->attribute( 'query', $query );
	}

	/**
	 * Create a route group.
	 *
	 * @param  Closure|string $routes Closure or path to file.
	 * @return void
	 */
	public function group( $routes ) {
		$this->router->group( $this->getAttributes(), $routes );
	}

	/**
	 * Create a route.
	 *
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function handle( $handler = '' ) {
		if ( ! empty( $handler ) ) {
			$this->attribute( 'handler', $handler );
		}

		$route = $this->router->route( $this->getAttributes() );

		$this->router->addRoute( $route );

		return $route;
	}

	/**
	 * Handle a request by directly rendering a view.
	 *
	 * @param  string|array<string> $views
	 * @return RouteInterface
	 */
	public function view( $views ) {
		return $this->handle( function () use ( $views ) {
			return $this->view_service->make( $views );
		} );
	}

	/**
	 * Match ALL requests.
	 *
	 * @param  string|Closure $handler
	 * @return RouteInterface
	 */
	public function all( $handler = '' ) {
		return $this->any()->url( '*' )->handle( $handler );
	}

	/**
	 * Match requests with a method of GET or HEAD.
	 *
	 * @return static $this
	 */
	public function get() {
		return $this->methods( ['GET', 'HEAD'] );
	}

	/**
	 * Match requests with a method of POST.
	 *
	 * @return static $this
	 */
	public function post() {
		return $this->methods( ['POST'] );
	}

	/**
	 * Match requests with a method of PUT.
	 *
	 * @return static $this
	 */
	public function put() {
		return $this->methods( ['PUT'] );
	}

	/**
	 * Match requests with a method of PATCH.
	 *
	 * @return static $this
	 */
	public function patch() {
		return $this->methods( ['PATCH'] );
	}

	/**
	 * Match requests with a method of DELETE.
	 *
	 * @return static $this
	 */
	public function delete() {
		return $this->methods( ['DELETE'] );
	}

	/**
	 * Match requests with a method of OPTIONS.
	 *
	 * @return static $this
	 */
	public function options() {
		return $this->methods( ['OPTIONS'] );
	}

	/**
	 * Match requests with any method.
	 *
	 * @return static $this
	 */
	public function any() {
		return $this->methods( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'] );
	}
}
