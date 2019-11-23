<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

/**
 * A helper file for aliased methods.
 */
namespace  {
	exit( 'This file should not be included, only analyzed by your IDE.' );
}

namespace WPEmerge\Application {

	use Psr\Http\Message\ResponseInterface;
	use WPEmerge\Requests\RequestInterface;
	use WPEmerge\Responses\RedirectResponse;
	use WPEmerge\View\ViewInterface;

	class Application {
		/**
		 * Create a "blank" response.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\Responses\ResponseService::response()
		 * @return ResponseInterface
		 */
		public static function response() {}

		/**
		 * Create a response with the specified string as its body.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\Responses\ResponseService::output()
		 * @param  string            $output
		 * @return ResponseInterface
		 */
		public static function output( $output ) {}

		/**
		 * Create a response with the specified data encoded as JSON as its body.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\Responses\ResponseService::json()
		 * @param  mixed             $data
		 * @return ResponseInterface
		 */
		public static function json( $data ) {}

		/**
		 * Create a redirect response.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\Responses\ResponseService::redirect()
		 * @return RedirectResponse
		 */
		public static function redirect() {}

		/**
		 * Create a view.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\View\ViewService::make()
		 * @param  string|array<string> $views
		 * @return ViewInterface
		 */
		public static function view( $views ) {}

		/**
		 * Create a response with the specified error status code.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\Responses\ResponseService::error()
		 * @param  integer           $status
		 * @return ResponseInterface
		 */
		public static function error( $status ) {}

		/**
		 * Output the specified view.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\View\ViewService::make()
		 * @see    \WPEmerge\View\ViewInterface::toString()
		 * @param  string|array<string> $views
		 * @param  array<string, mixed> $context
		 * @return void
		 */
		public static function render( $views, $context = [] ) {}

		/**
		 * Output child layout content.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\View\PhpViewEngine::getLayoutContent()
		 * @return void
		 */
		public static function layoutContent() {}

		/**
		 * Run a full middleware + handler pipeline independently of routes.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\Kernels\HttpKernel::run()
		 * @param  RequestInterface  $request
		 * @param  array<string>     $middleware
		 * @param  string|\Closure   $handler
		 * @param  array             $arguments
		 * @return ResponseInterface
		 */
		public static function run( RequestInterface $request, $middleware, $handler, $arguments = [] ) {}

		/**
		 * Get closure factory instance.
		 *
		 * @codeCoverageIgnore
		 * @return ClosureFactory
		 */
		public static function closure() {}
	}
}
