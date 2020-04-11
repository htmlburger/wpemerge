<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Kernels;

use Closure;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Helpers\Handler;
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
	 * @param  RequestInterface       $request
	 * @param  string[]               $middleware
	 * @param  string|Closure|Handler $handler
	 * @param  array                  $arguments
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
