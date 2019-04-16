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
 * Interface for ExecutesMiddlewareTrait.
 */
interface ExecutesMiddlewareInterface {
	/**
	 * Execute an array of middleware recursively (last in, first out).
	 *
	 * @param  array<string>     $middleware
	 * @param  RequestInterface  $request
	 * @param  Closure           $next
	 * @return ResponseInterface
	 */
	public function executeMiddleware( $middleware, RequestInterface $request, Closure $next );
}
