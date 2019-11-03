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
 * Interface for HasMiddlewareTrait.
 */
interface HasMiddlewareInterface {
	/**
	 * Get registered middleware.
	 *
	 * @return array<string>
	 */
	public function getMiddleware();

	/**
	 * Set registered middleware.
	 *
	 * @param  array<string> $middleware
	 * @return void
	 */
	public function setMiddleware( $middleware );

	/**
	 * Add middleware.
	 *
	 * @param  string|array<string> $middleware
	 * @return void
	 */
	public function addMiddleware( $middleware );

	/**
	 * Fluent alias for addMiddleware().
	 *
	 * @param  string|array $middleware
	 * @return static       $this
	 */
	public function middleware( $middleware );
}
