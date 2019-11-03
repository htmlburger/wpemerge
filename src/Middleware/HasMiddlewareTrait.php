<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

/**
 * Allow objects to have middleware.
 */
trait HasMiddlewareTrait {
	/**
	 * Array of all registered middleware.
	 *
	 * @var array<string>
	 */
	protected $middleware = [];

	/**
	 * Get registered middleware.
	 *
	 * @return array<string>
	 */
	public function getMiddleware() {
		return $this->middleware;
	}

	/**
	 * Set registered middleware.
	 *
	 * @param  array<string> $middleware
	 * @return void
	 */
	public function setMiddleware( $middleware ) {
		$this->middleware = (array) $middleware;
	}

	/**
	 * Add middleware.
	 *
	 * @param  string|array<string> $middleware
	 * @return void
	 */
	public function addMiddleware( $middleware ) {
		$this->setMiddleware( array_merge(
			$this->getMiddleware(),
			(array) $middleware
		) );
	}

	/**
	 * Fluent alias for addMiddleware().
	 *
	 * @codeCoverageIgnore
	 * @param  string|array $middleware
	 * @return static       $this
	 */
	public function middleware( $middleware ) {
		call_user_func_array( [$this, 'addMiddleware'], func_get_args() );

		return $this;
	}
}
