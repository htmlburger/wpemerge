<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
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
	 * @return array
	 */
	public function getMiddleware();

	/**
	 * Set registered middleware.
	 *
	 * @param  array $middleware
	 * @return void
	 */
	public function setMiddleware( $middleware );

	/**
	 * Add middleware.
	 *
	 * @param  string|\Closure|\WPEmerge\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function addMiddleware( $middleware );

	/**
	 * Fluent alias for addMiddleware().
	 * Accepts: a class name, an instance of a class, a Closure or an array of any of the previous.
	 *
	 * @param  string|\Closure|\WPEmerge\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function middleware( $middleware );
}
