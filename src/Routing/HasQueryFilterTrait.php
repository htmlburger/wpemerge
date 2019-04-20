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
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\UrlCondition;

/**
 * Represent an object which has a WordPress query filter.
 */
trait HasQueryFilterTrait {
	use HasConditionTrait;

	/**
	 * Query filter.
	 *
	 * @var callable|null
	 */
	protected $query_filter = null;

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
	 * @param  RequestInterface $request
	 * @param  array            $query_vars
	 * @return array
	 */
	public function applyQueryFilter( $request, $query_vars ) {
		$condition = $this->getCondition();

		if ( $this->getQueryFilter() === null ) {
			return $query_vars;
		}

		if ( ! $condition instanceof UrlCondition ) {
			throw new ConfigurationException(
				'Only routes with URL condition can use queries. ' .
				'Make sure your route has a URL condition and it is not in a non-URL route group.'
			);
		}

		$arguments = $this->getCondition()->getArguments( $request );

		return call_user_func_array( $this->getQueryFilter(), array_merge( [$query_vars], array_values( $arguments ) ) );
	}

	/**
	 * Fluent alias for setQueryFilter().
	 *
	 * @codeCoverageIgnore
	 * @param  callable $query_filter
	 * @return static   $this
	 */
	public function query( $query_filter ) {
		$this->setQueryFilter( $query_filter );

		return $this;
	}
}
