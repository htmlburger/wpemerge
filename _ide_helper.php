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
	use WPEmerge\Csrf\Csrf;
	use WPEmerge\Requests\RequestInterface;
	use WPEmerge\Responses\RedirectResponse;
	use WPEmerge\Routing\RouteBlueprint;
	use WPEmerge\View\ViewInterface;

	class Application {
		/**
		 * Get the Application instance.
		 *
		 * @codeCoverageIgnore
		 * @return \WPEmerge\Application\Application
		 */
		public static function app() {}

		/**
		 * Get the ClosureFactory instance.
		 *
		 * @codeCoverageIgnore
		 * @return ClosureFactory
		 */
		public static function closure() {}

		/**
		 * Get the CSRF service instance.
		 *
		 * @codeCoverageIgnore
		 * @return \WPEmerge\Csrf\Csrf
		 */
		public static function csrf() {}

		/**
		 * Get the Flash service instance.
		 *
		 * @codeCoverageIgnore
		 * @return \WPEmerge\Flash\Flash
		 */
		public static function flash() {}

		/**
		 * Get the OldInput service instance.
		 *
		 * @codeCoverageIgnore
		 * @return \WPEmerge\Input\OldInput
		 */
		public static function oldInput() {}

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
		 * Get the ResponseService instance.
		 *
		 * @codeCoverageIgnore
		 * @return \WPEmerge\Responses\ResponseService
		 */
		public static function responses() {}

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
		 * Create a response with the specified error status code.
		 *
		 * @codeCoverageIgnore
		 * @see    \WPEmerge\Responses\ResponseService::error()
		 * @param  integer           $status
		 * @return ResponseInterface
		 */
		public static function error( $status ) {}

		/**
		 * Create a new route.
		 *
		 * @codeCoverageIgnore
		 * @return RouteBlueprint
		 */
		public static function route() {}
		/**
		 * Get the ViewService instance.
		 *
		 * @codeCoverageIgnore
		 * @return \WPEmerge\View\ViewService
		 */
		public static function views() {}

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
	}
}
