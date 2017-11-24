<?php

use Obsidian\Response;

if ( ! function_exists( 'obs_response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_response() {
		return Response::response();
	}
}

if ( ! function_exists( 'obs_output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_output( $output ) {
		return Response::output( obs_response(), $output );
	}
}

if ( ! function_exists( 'obs_template' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::template()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_template( $templates, $context = array() ) {
		return Response::template( obs_response(), $templates, $context );
	}
}

if ( ! function_exists( 'obs_json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_json( $data ) {
		return Response::json( obs_response(), $data );
	}
}

if ( ! function_exists( 'obs_redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_redirect( $url, $status = 302 ) {
		return Response::redirect( obs_response(), $url, $status );
	}
}

if ( ! function_exists( 'obs_error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function obs_error( $code ) {
		return Response::error( obs_response(), $code );
	}
}
