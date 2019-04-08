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
use WPEmerge\Facades\Application;
use WPEmerge\Helpers\Handler;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\UrlCondition;

/**
 * Represent a route
 */
class Route implements RouteInterface {
	use HasMiddlewareTrait;

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
	 * Route handler.
	 *
	 * @var Handler
	 */
	protected $handler = null;

	/**
	 * Query filter.
	 *
	 * @var callable|null
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
	 * @codeCoverageIgnore
	 * @param  string[]           $methods
	 * @param  ConditionInterface $condition
	 * @param  string|\Closure    $handler
	 */
	public function __construct( $methods, $condition, $handler ) {
		$this->methods = $methods;
		$this->setCondition( $condition );
		$this->handler = new Handler( $handler, '', '\\App\\Controllers\\' );
	}

	/**
	 * Get allowed methods.
	 *
	 * @codeCoverageIgnore
	 * @return string[]
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function getCondition() {
		return $this->condition;
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function setCondition( $condition ) {
		$this->condition = $condition;
	}

	/**
	 * Get handler.
	 *
	 * @codeCoverageIgnore
	 * @return Handler
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * Set custom partial regex matching for the specified parameter.
	 *
	 * @throws ConfigurationException
	 * @param  string $parameter
	 * @param  string $regex
	 * @return static $this
	 */
	public function where( $parameter, $regex ) {
		$condition = $this->getCondition();

		if ( ! $condition instanceof UrlCondition ) {
			throw new ConfigurationException( 'Only routes with URL conditions can specify parameter regex matching.' );
		}

		$condition->setUrlWhere( array_merge(
			$condition->getUrlWhere(),
			[$parameter => $regex]
		) );

		return $this;
	}

	/**
	 * Get the main WordPress query vars filter, if any.
	 *
	 * @codeCoverageIgnore
	 * @return callable|null
	 */
	public function getQueryFilter() {
		return $this->query_filter;
	}

	/**
	 * Set the main WordPress query vars filter.
	 *
	 * @codeCoverageIgnore
	 * @param  callable|null $query_filter
	 * @return void
	 */
	public function setQueryFilter( $query_filter ) {
		$this->query_filter = $query_filter;
	}

	/**
	 * Apply the query filter, if any.
	 *
	 * @internal
	 * @throws ConfigurationException
	 * @param RequestInterface            $request
	 * @param  array<string, mixed>       $query_vars
	 * @return array<string, mixed>|false
	 */
	public function applyQueryFilter( $request, $query_vars ) {
		$condition = $this->getCondition();

		if ( $this->getQueryFilter() === null ) {
			return false;
		}

		if ( ! $condition instanceof UrlCondition ) {
			throw new ConfigurationException(
				'Only routes with URL condition can use queries. ' .
				'Make sure your route has a URL condition and it is not in a non-URL route group.'
			);
		}

		if ( ! $this->getCondition()->isSatisfied( $request ) ) {
			return false;
		}

		$arguments = $this->getCondition()->getArguments( $request );

		return call_user_func_array( $this->getQueryFilter(), array_merge( [$query_vars], array_values( $arguments ) ) );
	}

	/**
	 * Fluent alias of setQueryFilter().
	 *
	 * @codeCoverageIgnore
	 * @param  callable $query_filter
	 * @return static   $this
	 */
	public function query( $query_filter ) {
		$this->setQueryFilter( $query_filter );

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
		$arguments = array_merge(
			[$request, $view],
			array_values( $this->condition->getArguments( $request ) )
		);

		return call_user_func_array( [$this->getHandler(), 'execute'], $arguments );
	}
}
