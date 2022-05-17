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
use WPEmerge\Helpers\HasAttributesTrait;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\View\ViewService;

/**
 * Provide a fluent interface for registering routes with the router.
 */
class RouteBlueprint {
	use HasAttributesTrait;

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
	 * Allowed WordPress conditional tags
	 *
	 * @var string[]
	 */
	protected array $allowedConditionals = [
		'is_404',
		'is_archive',
		'is_attachment',
		'is_author',
		'is_category',
		'is_date',
		'is_day',
		'is_front_page',
		'is_home',
		'is_month',
		'is_page',
		'is_page_template',
		'is_paged',
		'is_post_type_archive',
		'is_privacy_policy',
		'is_search',
		'is_single',
		'is_singular',
		'is_sticky',
		'is_tag',
		'is_tax',
		'is_time',
		'is_year',
	];

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
	 * Call WordPress conditional tag
	 *
	 * @param  string $name
	 * @param  array $arguments
	 * @return static $this
	 */
	public function __call( $name, $arguments ) {
		if ( in_array( $name, $this->getAllowedConditionals(), true ) ) {
			return $this->get()->where( $name, ...$arguments );
		}

		throw new \InvalidArgumentException( sprintf( 'Method `%s()` is not allowed WordPress conditional tag', $name ) );
	}

	/**
	 * Get list of allowed conditional tags
	 *
	 * @return string[]
	 */
	public function getAllowedConditionals() {
		return $this->allowedConditionals;
	}

	/**
	 * Handle ajax requests
	 *
	 * @param  string $action
	 * @param  boolean $private
	 * @param  boolean $public
	 * @return static   $this
	 */
	public function ajax( $action, $private = true, $public = false )
	{
		return $this->where( 'ajax', $action, $private, $public );
	}

	/**
	 * Match requests using one of the specified methods.
	 *
	 * @param  string[] $methods
	 * @return static   $this
	 */
	public function methods( $methods ) {
		$methods = $this->router->mergeMethodsAttribute(
			(array) $this->getAttribute( 'methods', [] ),
			(array) $methods
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
			$this->getAttribute( 'condition', null ),
			$condition
		);

		return $this->attribute( 'condition', $condition );
	}

	/**
	 * Set the middleware attribute.
	 *
	 * @param  string|string[] $middleware
	 * @return static          $this
	 */
	public function middleware( $middleware ) {
		$middleware = $this->router->mergeMiddlewareAttribute(
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
		$namespace = $this->router->mergeNamespaceAttribute(
			$this->getAttribute( 'namespace', '' ),
			$namespace
		);

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
	 * Set the name attribute.
	 *
	 * @param  string $name
	 * @return static $this
	 */
	public function name( $name ) {
		return $this->attribute( 'name', $name );
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
	 * @return void
	 */
	public function handle( $handler = '' ) {
		if ( ! empty( $handler ) ) {
			$this->attribute( 'handler', $handler );
		}

		$route = $this->router->route( $this->getAttributes() );

		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 );

		if ( ! empty( $trace ) && ! empty( $trace[0]['file'] ) ) {
			$route->attribute( '__definition', $trace[0]['file'] . ':' . $trace[0]['line'] );
		}

		$this->router->addRoute( $route );
	}

	/**
	 * Handle a request by directly rendering a view.
	 *
	 * @param  string|string[] $views
	 * @return void
	 */
	public function view( $views ) {
		$this->handle( function () use ( $views ) {
			return $this->view_service->make( $views );
		} );
	}

	/**
	 * Match ALL requests.
	 *
	 * @param  string|Closure $handler
	 * @return void
	 */
	public function all( $handler = '' ) {
		$this->any()->url( '*' )->handle( $handler );
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
