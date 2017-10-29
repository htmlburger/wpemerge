<?php

use CarbonFramework\Response;

if ( ! function_exists( 'cf_response' ) ) {
	/**
	 * @see Response::response()
	 * @codeCoverageIgnore
	 */
	function cf_response() {
		return Response::response();
	}
}

if ( ! function_exists( 'cf_output' ) ) {
	/**
	 * @see Response::output()
	 * @codeCoverageIgnore
	 */
	function cf_output( $output ) {
		return Response::output( cf_response(), $output );
	}
}

if ( ! function_exists( 'cf_template' ) ) {
	/**
	 * @see Response::template()
	 * @codeCoverageIgnore
	 */
	function cf_template( $templates, $context = array() ) {
		return Response::template( cf_response(), $templates, $context );
	}
}

if ( ! function_exists( 'cf_json' ) ) {
	/**
	 * @see Response::json()
	 * @codeCoverageIgnore
	 */
	function cf_json( $data ) {
		return Response::json( cf_response(), $data );
	}
}

if ( ! function_exists( 'cf_redirect' ) ) {
	/**
	 * @see Response::redirect()
	 * @codeCoverageIgnore
	 */
	function cf_redirect( $url, $status = 302 ) {
		return Response::redirect( cf_response(), $url, $status );
	}
}

if ( ! function_exists( 'cf_error' ) ) {
	/**
	 * @see Response::error()
	 * @codeCoverageIgnore
	 */
	function cf_error( $code ) {
		return Response::error( cf_response(), $code );
	}
}
