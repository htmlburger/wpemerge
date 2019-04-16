<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

use WPEmerge\Facades\Response;
use WPEmerge\Facades\View;

if ( ! function_exists( 'app_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\Responses\ResponseService::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_response() {
		return call_user_func_array( [Response::class, 'response'], func_get_args() );
	}
}

if ( ! function_exists( 'app_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\Responses\ResponseService::output()
	 * @param  string                              $output
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_output( $output ) {
		return call_user_func_array( [Response::class, 'output'], func_get_args() );
	}
}

if ( ! function_exists( 'app_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\Responses\ResponseService::json()
	 * @param  mixed                               $data
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_json( $data ) {
		return call_user_func_array( [Response::class, 'json'], func_get_args() );
	}
}

if ( ! function_exists( 'app_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\Responses\ResponseService::redirect()
	 * @return \WPEmerge\Responses\RedirectResponse
	 */
	function app_redirect() {
		return call_user_func_array( [Response::class, 'redirect'], func_get_args() );
	}
}

if ( ! function_exists( 'app_view' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\Responses\ResponseService::view()
	 * @param  string|array<string>         $views
	 * @return \WPEmerge\View\ViewInterface
	 */
	function app_view( $views ) {
		return call_user_func_array( [Response::class, 'view'], func_get_args() );
	}
}

if ( ! function_exists( 'app_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\Responses\ResponseService::error()
	 * @param  integer                             $status
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_error( $status ) {
		return call_user_func_array( [Response::class, 'error'], func_get_args() );
	}
}

if ( ! function_exists( 'app_render' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\View\ViewService::make()
	 * @see    \WPEmerge\View\ViewInterface::toString()
	 * @param  string|array<string> $views
	 * @param  array<string, mixed> $context
	 * @return void
	 */
	function app_render( $views, $context = [] ) {
		$view = app_view( $views )->with( $context );
		View::triggerPartialHooks( $view->getName() );
		echo $view->toString();
	}
}

if ( ! function_exists( 'app_layout_content' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\View\PhpView::getLayoutContent()
	 * @return void
	 */
	function app_layout_content() {
		echo \WPEmerge\View\PhpView::getLayoutContent();
	}
}

if ( ! function_exists( 'app_run' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see    \WPEmerge\Kernels\HttpKernel::run()
	 * @param  \WPEmerge\Requests\RequestInterface $request
	 * @param  array<string>                       $middleware
	 * @param  string|\Closure                     $handler
	 * @param  array                               $arguments
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_run( \WPEmerge\Requests\RequestInterface $request, $middleware, $handler, $arguments = [] ) {
		$kernel = \WPEmerge\Facades\Application::resolve( WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY );
		return call_user_func_array( [$kernel, 'run'], func_get_args() );
	}
}
