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
use WPEmerge\Facades\Application;
use WPEmerge\Requests\RequestInterface;

/**
 * Allow objects to execute middleware.
 */
trait ExecutesMiddlewareTrait {
	/**
	 * Execute an array of middleware recursively (last in, first out).
	 *
	 * @param  array<array<string>>                $middleware
	 * @param  RequestInterface                    $request
	 * @param  Closure                             $next
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function executeMiddleware( $middleware, RequestInterface $request, Closure $next ) {
		$top_middleware = array_shift( $middleware );

		if ( $top_middleware === null ) {
			return $next( $request );
		}

		$top_middleware_next = function ( $request ) use ( $middleware, $next ) {
			return $this->executeMiddleware( $middleware, $request, $next );
		};

		$class = $top_middleware[0];
		$arguments = array_merge(
			[$request, $top_middleware_next],
			array_slice( $top_middleware, 1 )
		);
		$instance = Application::instantiate( $class );

		return call_user_func_array( [$instance, 'handle'], $arguments );
	}
}
