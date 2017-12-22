<?php

if ( ! function_exists( 'app_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_response() {
		return call_user_func_array( '\WPEmerge\response', func_get_args() );
	}
}

if ( ! function_exists( 'app_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_output() {
		return call_user_func_array( '\WPEmerge\output', func_get_args() );
	}
}

if ( ! function_exists( 'app_view' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\view()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_view() {
		return call_user_func_array( '\WPEmerge\view', func_get_args() );
	}
}

if ( ! function_exists( 'app_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_json() {
		return call_user_func_array( '\WPEmerge\json', func_get_args() );
	}
}

if ( ! function_exists( 'app_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_redirect() {
		return call_user_func_array( '\WPEmerge\redirect', func_get_args() );
	}
}

if ( ! function_exists( 'app_reload' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\reload()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_reload() {
		return call_user_func_array( '\WPEmerge\reload', func_get_args() );
	}
}

if ( ! function_exists( 'app_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function app_error() {
		return call_user_func_array( '\WPEmerge\error', func_get_args() );
	}
}

if ( ! function_exists( 'app_partial' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see \WPEmerge\partial()
	 * @return void
	 */
	function app_partial() {
		return call_user_func_array( '\WPEmerge\partial', func_get_args() );
	}
}
