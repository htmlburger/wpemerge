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
interface HasControllerMiddlewareInterface {
	/**
	 * Get middleware.
	 *
	 * @param  string        $method
	 * @return array<string>
	 */
	public function getMiddleware( $method );

	/**
	 * Add middleware.
	 *
	 * @param  string|array<string> $middleware
	 * @return ControllerMiddleware
	 */
	public function addMiddleware( $middleware );

	/**
	 * Fluent alias for addMiddleware().
	 *
	 * @param  string|array<string> $middleware
	 * @return ControllerMiddleware
	 */
	public function middleware( $middleware );
}
