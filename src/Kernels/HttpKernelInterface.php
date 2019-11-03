<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Kernels;

use Closure;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Middleware\HasMiddlewareDefinitionsInterface;
use WPEmerge\Requests\RequestInterface;

/**
 * Describes how a request is handled.
 */
interface HttpKernelInterface extends HasMiddlewareDefinitionsInterface {
	/**
	 * Bootstrap the kernel.
	 *
	 * @return void
	 */
	public function bootstrap();

	/**
	 * Run a response pipeline for the given request.
	 *
	 * @param  RequestInterface  $request
	 * @param  array<string>     $middleware
	 * @param  string|Closure    $handler
	 * @param  array             $arguments
	 * @return ResponseInterface
	 */
	public function run( RequestInterface $request, $middleware, $handler, $arguments = [] );

	/**
	 * Return a response for the given request.
	 *
	 * @param  RequestInterface       $request
	 * @param  array                  $arguments
	 * @return ResponseInterface|null
	 */
	public function handle( RequestInterface $request, $arguments = [] );
}
