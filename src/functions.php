<?php

use WPEmerge\Facades\Framework;
use WPEmerge\Response;
use WPEmerge\Helpers\Mixed;

if ( ! function_exists( 'app_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_response() {
		return call_user_func_array( [Response::class, 'response'], func_get_args() );
	}
}

if ( ! function_exists( 'app_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_output( $output ) {
		return call_user_func_array( [Response::class, 'output'], func_get_args() );
	}
}

if ( ! function_exists( 'app_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_json( $data ) {
		return call_user_func_array( [Response::class, 'json'], func_get_args() );
	}
}

if ( ! function_exists( 'app_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_redirect( $url, $status = 302 ) {
		return call_user_func_array( [Response::class, 'redirect'], func_get_args() );
	}
}

if ( ! function_exists( 'app_reload' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::reload()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_reload( $request, $status = 302 ) {
		return call_user_func_array( [Response::class, 'reload'], func_get_args() );
	}
}

if ( ! function_exists( 'app_view' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::view()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_view( $views, $context = [] ) {
		return call_user_func_array( [Response::class, 'view'], func_get_args() );
	}
}

if ( ! function_exists( 'app_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_error( $code ) {
		return call_user_func_array( [Response::class, 'error'], func_get_args() );
	}
}

if ( ! function_exists( 'app_partial' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see WPEmerge\View\PhpViewEngine::make()
	 * @see WPEmerge\View\PhpView::toString()
	 * @return void
	 */
	function app_partial( $views, $context = [] ) {
		$views = Mixed::toArray( $views );
		$engine = Framework::resolve( WPEMERGE_VIEW_ENGINE_PHP_KEY );
		echo $engine->make( $views, $context )->toString();
	}
}
