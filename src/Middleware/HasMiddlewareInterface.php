<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Requests\RequestInterface;

/**
 * Interface for HasMiddlewareTrait
 */
interface HasMiddlewareInterface {
	/**
	 * Get registered middleware
	 *
	 * @return array
	 */
	public function getMiddleware();

	/**
	 * Add middleware
	 *
	 * @param  string|\Closure|\WPEmerge\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function addMiddleware( $middleware );

	/**
	 * Alias for addMiddleware.
	 * Accepts: a class name, an instance of a class, a Closure or an array of any of the previous
	 *
	 * @param  string|\Closure|\WPEmerge\Middleware\MiddlewareInterface|array $middleware
	 * @return object                                                         $this
	 */
	public function add( $middleware );

	/**
	 * Execute an array of middleware recursively (last in, first out)
	 *
	 * @param  array             $middleware
	 * @param  RequestInterface  $request
	 * @param  Closure           $next
	 * @return ResponseInterface
	 */
	public function executeMiddleware( $middleware, RequestInterface $request, Closure $next );
}
