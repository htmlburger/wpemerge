<?php

use Obsidian\Response;

if ( ! function_exists( 'cf_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function cf_response() {
		return Response::response();
	}
}

if ( ! function_exists( 'cf_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function cf_output( $output ) {
		return Response::output( cf_response(), $output );
	}
}

if ( ! function_exists( 'cf_template' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::template()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function cf_template( $templates, $context = array() ) {
		return Response::template( cf_response(), $templates, $context );
	}
}

if ( ! function_exists( 'cf_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function cf_json( $data ) {
		return Response::json( cf_response(), $data );
	}
}

if ( ! function_exists( 'cf_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function cf_redirect( $url, $status = 302 ) {
		return Response::redirect( cf_response(), $url, $status );
	}
}

if ( ! function_exists( 'cf_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function cf_error( $code ) {
		return Response::error( cf_response(), $code );
	}
}
