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
use WPEmerge\Requests\RequestInterface;

/**
 * Interface that middleware must implement
 */
interface MiddlewareInterface {
	/**
	 * Execute middleware
	 *
	 * @param  RequestInterface                    $request
	 * @param  Closure                             $next
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handle( RequestInterface $request, Closure $next );
}
