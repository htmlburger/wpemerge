<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge;

use WPEmerge\Facades\Response;
use WPEmerge\Facades\View;

/**
 * Create a "blank" response.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\Responses\ResponseService::response()
 * @return \Psr\Http\Message\ResponseInterface
 */
function response() {
	return call_user_func_array( [Response::class, 'response'], func_get_args() );
}

/**
 * Create a response with the specified string as its body.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\Responses\ResponseService::output()
 * @param  string                              $output
 * @return \Psr\Http\Message\ResponseInterface
 */
function output( $output ) {
	return call_user_func_array( [Response::class, 'output'], func_get_args() );
}

/**
 * Create a response with the specified data encoded as JSON as its body.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\Responses\ResponseService::json()
 * @param  mixed                               $data
 * @return \Psr\Http\Message\ResponseInterface
 */
function json( $data ) {
	return call_user_func_array( [Response::class, 'json'], func_get_args() );
}

/**
 * Create a redirect response.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\Responses\ResponseService::redirect()
 * @return \WPEmerge\Responses\RedirectResponse
 */
function redirect() {
	return call_user_func_array( [Response::class, 'redirect'], func_get_args() );
}

/**
 * Create a view.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\Responses\ResponseService::view()
 * @param  string|array<string>         $views
 * @return \WPEmerge\View\ViewInterface
 */
function view( $views ) {
	return call_user_func_array( [Response::class, 'view'], func_get_args() );
}

/**
 * Create a response with the specified error status code.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\Responses\ResponseService::error()
 * @param  integer                             $status
 * @return \Psr\Http\Message\ResponseInterface
 */
function error( $status ) {
	return call_user_func_array( [Response::class, 'error'], func_get_args() );
}

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
function render( $views, $context = [] ) {
	$view = view( $views )->with( $context );
	View::triggerPartialHooks( $view->getName() );
	echo $view->toString();
}

/**
 * Output child layout content.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\View\PhpView::getLayoutContent()
 * @return void
 */
function layout_content() {
	echo \WPEmerge\View\PhpView::getLayoutContent();
}

/**
 * Run a full middleware + handler pipeline independently of routes.
 *
 * @codeCoverageIgnore
 * @see    \WPEmerge\Kernels\HttpKernel::run()
 * @param  \WPEmerge\Requests\RequestInterface $request
 * @param  array<string>                       $middleware
 * @param  string|\Closure                     $handler
 * @param  array                               $arguments
 * @return \Psr\Http\Message\ResponseInterface
 */
function run( \WPEmerge\Requests\RequestInterface $request, $middleware, $handler, $arguments = [] ) {
	$kernel = \WPEmerge\Facades\Application::resolve( WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY );
	return call_user_func_array( [$kernel, 'run'], func_get_args() );
}
