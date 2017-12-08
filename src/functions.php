<?php

use WPEmerge\Response;

if ( ! function_exists( 'wpm_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function wpm_response() {
		return Response::response();
	}
}

if ( ! function_exists( 'wpm_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function wpm_output( $output ) {
		return Response::output( wpm_response(), $output );
	}
}

if ( ! function_exists( 'wpm_view' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::view()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function wpm_view( $views, $context = array() ) {
		return Response::view( wpm_response(), $views, $context );
	}
}

if ( ! function_exists( 'wpm_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function wpm_json( $data ) {
		return Response::json( wpm_response(), $data );
	}
}

if ( ! function_exists( 'wpm_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function wpm_redirect( $url, $status = 302 ) {
		return Response::redirect( wpm_response(), $url, $status );
	}
}

if ( ! function_exists( 'wpm_reload' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::reload()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function wpm_reload( $request, $status = 302 ) {
		return Response::reload( wpm_response(), $request, $status );
	}
}

if ( ! function_exists( 'wpm_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function wpm_error( $code ) {
		return Response::error( wpm_response(), $code );
	}
}
