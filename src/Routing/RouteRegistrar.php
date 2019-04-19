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
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Support\Arr;

/**
 * Provide a fluent interface for registering routes with the router.
 */
class RouteRegistrar {
	/**
	 * Condition factory.
	 *
	 * @var ConditionFactory
	 */
	protected $condition_factory = null;

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
	 * Group stack.
	 *
	 * @var array<array<string, mixed>>
	 */
	protected $group_stack = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ConditionFactory $condition_factory
	 * @param Router           $router
	 */
	public function __construct( ConditionFactory $condition_factory, Router $router ) {
		$this->condition_factory = $condition_factory;
		$this->router = $router;
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
	 * Reset attributes.
	 *
	 * @return void
	 */
	public function resetAttributes() {
		$this->setAttributes( [] );
	}

	/**
	 * Fluent alias for setAttributes().
	 *
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
			$arguments = array_slice( func_get_args(), 1 );
			$condition = array_merge( [$condition], $arguments );
		}

		$condition = $this->condition_factory->merge(
			$this->getAttribute( 'condition' ),
			$condition
		);

		return $this->attribute( 'condition', $condition !== null ? $condition : '' );
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
	 * Make a route condition.
	 *
	 * @param  mixed              $condition
	 * @return ConditionInterface
	 */
	public function makeRouteCondition( $condition ) {
		if ( ! $condition instanceof ConditionInterface ) {
			try {
				$condition = $this->condition_factory->make( $condition );
			} catch ( ConfigurationException $e ) {
				throw new ConfigurationException( 'Route condition is not a valid route string or condition.' );
			}
		}

		return $condition;
	}

	/**
	 * Make a route handler.
	 *
	 * @param  string|\Closure|null $handler
	 * @param  string               $namespace
	 * @return Handler
	 */
	public function makeRouteHandler( $handler, $namespace ) {
		if ( $handler === null ) {
			$handler = $this->getAttribute( 'controller', '' );
		}

		$handler = new Handler( $handler, '', $namespace );

		return $handler;
	}

	/**
	 * Make a route.
	 *
	 * @param  string|\Closure|null $handler
	 * @param  array<string, mixed> $attributes
	 * @return RouteInterface
	 */
	public function makeRoute( $handler, $attributes ) {
		$methods = Arr::get( $attributes, 'methods', [] );
		$condition = Arr::get( $attributes, 'condition', null );
		$namespace = Arr::get( $attributes, 'namespace', '' );
		$middleware = Arr::get( $attributes, 'middleware', [] );

		$condition = $this->makeRouteCondition( $condition );
		$handler = $this->makeRouteHandler( $handler, $namespace );

		$route = new Route( $methods, $condition, $handler );

		if ( ! empty( $middleware ) ) {
			$route->middleware( $middleware );
		}

		return $route;
	}

	/**
	 * Merge attributes into route.
	 *
	 * @param  array<string, mixed> $old
	 * @param  array<string, mixed> $new
	 * @return array<string, mixed>
	 */
	public function mergeAttributes( $old, $new ) {
		$condition = $this->condition_factory->merge(
			Arr::get( $old, 'condition', '' ),
			Arr::get( $new, 'condition', '' )
		);

		$attributes = [
			'methods' => array_merge(
				(array) Arr::get( $old, 'methods', [] ),
				(array) Arr::get( $new, 'methods', [] )
			),
			'condition' => $condition !== null ? $condition : '',
			'middleware' => array_merge(
				(array) Arr::get( $old, 'middleware', [] ),
				(array) Arr::get( $new, 'middleware', [] )
			),
			'namespace' => Arr::get( $new, 'namespace', Arr::get( $old, 'namespace', '' ) ),
			'controller' => Arr::get( $new, 'controller', Arr::get( $old, 'controller', '' ) ),
		];

		return $attributes;
	}

	/**
	 * Get the top group from the stack.
	 *
	 * @return array<string, mixed>
	 */
	protected function getGroup() {
		return Arr::last( $this->group_stack, null, [] );
	}

	/**
	 * Add a group to the group stack, merging all previous attributes.
	 *
	 * @param array<string, mixed> $group
	 * @return void
	 */
	protected function pushGroup( $group ) {
		$this->group_stack[] = $this->mergeAttributes( $this->getGroup(), $group );
	}

	/**
	 * Remove last group from the group stack.
	 *
	 * @return void
	 */
	protected function popGroup() {
		array_pop( $this->group_stack );
	}

	/**
	 * Create a route group.
	 *
	 * @param \Closure|string $routes Closure or path to file.
	 * @return void
	 */
	public function group( $routes ) {
		$this->pushGroup( $this->getAttributes() );

		$this->resetAttributes();

		if ( is_string( $routes ) ) {
			require_once $routes;
		} else {
			$routes();
		}

		$this->popGroup();

		$this->resetAttributes();
	}

	/**
	 * Create a route.
	 *
	 * @param  string|\Closure|null $handler
	 * @return void
	 */
	public function to( $handler ) {
		$attributes = $this->mergeAttributes( $this->getGroup(), $this->getAttributes() );
		$route = $this->makeRoute( $handler, $attributes );
		$this->router->addRoute( $route );

		$this->resetAttributes();
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

	/**
	 * Match ALL requests.
	 *
	 * @return static $this
	 */
	public function all() {
		return $this->any()->url( '*' );
	}
}
