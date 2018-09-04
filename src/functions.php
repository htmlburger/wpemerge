<?php

use WPEmerge\Facades\Response;
use WPEmerge\Facades\View;

if ( ! function_exists( 'app_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\Responses\ResponseService::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_response() {
		return call_user_func_array( [Response::class, 'response'], func_get_args() );
	}
}

if ( ! function_exists( 'app_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\Responses\ResponseService::output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_output( $output ) {
		return call_user_func_array( [Response::class, 'output'], func_get_args() );
	}
}

if ( ! function_exists( 'app_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\Responses\ResponseService::json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_json( $data ) {
		return call_user_func_array( [Response::class, 'json'], func_get_args() );
	}
}

if ( ! function_exists( 'app_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\Responses\ResponseService::redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_redirect() {
		return call_user_func_array( [Response::class, 'redirect'], func_get_args() );
	}
}

if ( ! function_exists( 'app_view' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\Responses\ResponseService::view()
	 * @return \WPEmerge\View\ViewInterface
	 */
	function app_view( $views ) {
		return call_user_func_array( [Response::class, 'view'], func_get_args() );
	}
}

if ( ! function_exists( 'app_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\Responses\ResponseService::error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_error( $code ) {
		return call_user_func_array( [Response::class, 'error'], func_get_args() );
	}
}

if ( ! function_exists( 'app_partial' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\View\ViewService::make()
	 * @see \WPEmerge\View\ViewInterface::toString()
	 * @return void
	 */
	function app_partial( $views, $context = [] ) {
		$view = View::make( $views )->with( $context );
		View::triggerPartialHooks( $view->getName() );
	    echo $view->toString();
	}
}

if ( ! function_exists( 'app_layout_content' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\View\PhpView::getLayoutContent()
	 * @return void
	 */
	function app_layout_content() {
		echo \WPEmerge\View\PhpView::getLayoutContent();
	}
}
