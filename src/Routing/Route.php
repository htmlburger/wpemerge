<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use Closure;
use WPEmerge\Exceptions\Exception;
use WPEmerge\Facades\Framework;
use WPEmerge\Facades\RouteCondition;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\InvalidRouteConditionException;
use WPEmerge\Routing\Conditions\UrlCondition;

/**
 * Represent a route
 */
class Route implements RouteInterface {
	/**
	 * Allowed methods.
	 *
	 * @var string[]
	 */
	protected $methods = [];

	/**
	 * Route condition.
	 *
	 * @var ConditionInterface
	 */
	protected $condition = null;

	/**
	 * Route pipeline.
	 *
	 * @var Pipeline
	 */
	protected $pipeline = null;

	/**
	 * Query filter.
	 *
	 * @var callable
	 */
	protected $query_filter = null;

	/**
	 * Query filter action priority.
	 *
	 * @var integer
	 */
	protected $query_filter_priority = 1000;

	/**
	 * Constructor.
	 *
	 * @throws Exception
	 * @param  string[]        $methods
	 * @param  mixed           $condition
	 * @param  string|\Closure $handler
	 */
	public function __construct( $methods, $condition, $handler ) {
		if ( ! $condition instanceof ConditionInterface ) {
			try {
				$condition = RouteCondition::make( $condition );
			} catch ( InvalidRouteConditionException $e ) {
				throw new Exception( 'Route condition is not a valid route string or condition.' );
			}
		}

		$this->methods = $methods;
		$this->condition = $condition;
		$this->pipeline = new Pipeline( $handler );
	}

	/**
	 * Get allowed methods.
	 *
	 * @return string[]
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * Get condition.
	 *
	 * @return ConditionInterface
	 */
	public function getCondition() {
		return $this->condition;
	}

	/**
	 * Get pipeline.
	 *
	 * @return Pipeline
	 */
	public function getPipeline() {
		return $this->pipeline;
	}

	/**
	 * Get the main WordPress query vars filter, if any.
	 *
	 * @return callable|null
	 */
	public function getQueryFilter() {
		return $this->query_filter;
	}

	/**
	 * Set the main WordPress query vars filter and add it to the appropriate WordPress action.
	 *
	 * @param  callable|null $query_filter
	 * @return void
	 */
	public function setQueryFilter( $query_filter ) {
		$this->query_filter = $query_filter;
	}

	/**
	 * Add the query filter to the appropriate WordPress action.
	 *
	 * @return void
	 */
	public function addQueryFilter() {
		$filter = [$this, 'applyQueryFilter'];

		if ( ! has_action( 'request', $filter ) ) {
			add_action( 'request', $filter, $this->query_filter_priority );
		}
	}

	/**
	 * Remove the query filter from the appropriate WordPress action.
	 *
	 * @return void
	 */
	public function removeQueryFilter() {
		$filter = [$this, 'applyQueryFilter'];

		if ( has_action( 'request', $filter ) ) {
			remove_action( 'request', $filter, $this->query_filter_priority );
		}
	}

	/**
	 * Apply the query filter, if any.
	 *
	 * @throws Exception
	 * @param  array<string, mixed> $query_vars
	 * @return array<string, mixed>
	 */
	public function applyQueryFilter( $query_vars ) {
		$request = Framework::resolve( WPEMERGE_REQUEST_KEY );
		$condition = $this->getCondition();

		if ( ! is_callable( $this->getQueryFilter() ) ) {
			return $query_vars;
		}

		if ( ! $condition instanceof UrlCondition ) {
			throw new Exception(
				'Routes with queries can only use URL conditions. ' .
				'Is the route in a non-URL route group?'
			);
		}

		if ( $this->getCondition()->isSatisfied( $request ) ) {
			$arguments = $this->getCondition()->getArguments( $request );
			$query_vars = call_user_func_array( $this->getQueryFilter(), array_merge( [$query_vars], $arguments ) );
		}

		return $query_vars;
	}

	/**
	 * Set the main WordPress query vars filter.
	 *
	 * @codeCoverageIgnore
	 * @param  callable $query_filter
	 * @return self     $this
	 */
	public function query( $query_filter ) {
		$this->setQueryFilter( $query_filter );
		$this->addQueryFilter();
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		if ( ! in_array( $request->getMethod(), $this->methods ) ) {
			return false;
		}
		return $this->condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return $this->getCondition()->getArguments( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, $view ) {
		$arguments = array_merge( [$request, $view], $this->condition->getArguments( $request ) );

		return $this->getPipeline()->run( $request, $arguments );
	}

	/**
	 * @codeCoverageIgnore
	 * {@inheritDoc}
	 */
	public function getMiddleware() {
		return $this->getPipeline()->getMiddleware();
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function setMiddleware( $middleware ) {
		$this->getPipeline()->setMiddleware( $middleware );
	}

	/**
	 * @codeCoverageIgnore
	 * @throws Exception
	 * {@inheritDoc}
	 */
	public function addMiddleware( $middleware ) {
		$this->getPipeline()->addMiddleware( $middleware );

		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 * {@inheritDoc}
	 */
	public function add( $middleware ) {
		$this->getPipeline()->add( $middleware );

		return $this;
	}

	/**
	 * @codeCoverageIgnore
	 * {@inheritDoc}
	 */
	public function executeMiddleware( $middleware, RequestInterface $request, Closure $next ) {
		return $this->getPipeline()->executeMiddleware( $middleware, $request, $next );
	}
}
