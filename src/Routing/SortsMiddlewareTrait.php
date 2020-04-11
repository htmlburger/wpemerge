<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

/**
 * Provide middleware sorting.
 */
trait SortsMiddlewareTrait {
	/**
	 * Middleware sorted in order of execution.
	 *
	 * @var string[]
	 */
	protected $middleware_priority = [];

	/**
	 * Get middleware execution priority.
	 *
	 * @codeCoverageIgnore
	 * @return string[]
	 */
	public function getMiddlewarePriority() {
		return $this->middleware_priority;
	}

	/**
	 * Set middleware execution priority.
	 *
	 * @codeCoverageIgnore
	 * @param  string[] $middleware_priority
	 * @return void
	 */
	public function setMiddlewarePriority( $middleware_priority ) {
		$this->middleware_priority = $middleware_priority;
	}

	/**
	 * Get priority for a specific middleware.
	 * This is in reverse compared to definition order.
	 * Middleware with unspecified priority will yield -1.
	 *
	 * @param  string|array $middleware
	 * @return integer
	 */
	public function getMiddlewarePriorityForMiddleware( $middleware ) {
		if ( is_array( $middleware ) ) {
			$middleware = $middleware[0];
		}

		$increasing_priority = array_reverse( $this->getMiddlewarePriority() );
		$priority = array_search( $middleware, $increasing_priority );
		return $priority !== false ? (int) $priority : -1;
	}

	/**
	 * Sort array of fully qualified middleware class names by priority in ascending order.
	 *
	 * @param  string[] $middleware
	 * @return array
	 */
	public function sortMiddleware( $middleware ) {
		$sorted = $middleware;

		usort( $sorted, function ( $a, $b ) use ( $middleware ) {
			$a_priority = $this->getMiddlewarePriorityForMiddleware( $a );
			$b_priority = $this->getMiddlewarePriorityForMiddleware( $b );
			$priority = $b_priority - $a_priority;

			if ( $priority !== 0 ) {
				return $priority;
			}

			// Keep relative order from original array.
			return array_search( $a, $middleware ) - array_search( $b, $middleware );
		} );

		return array_values( $sorted );
	}
}
