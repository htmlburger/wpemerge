<?php

namespace WPEmerge;

use WPEmerge\Facades\Framework;
use WPEmerge\Response;
use WPEmerge\Helpers\Mixed;

if ( ! function_exists( 'response' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::response()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function response() {
		return Response::response();
	}
}

if ( ! function_exists( 'output' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::output()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function output( $output ) {
		return Response::output( response(), $output );
	}
}

if ( ! function_exists( 'view' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::view()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function view( $views, $context = array() ) {
		return Response::view( response(), $views, $context );
	}
}

if ( ! function_exists( 'json' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::json()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function json( $data ) {
		return Response::json( response(), $data );
	}
}

if ( ! function_exists( 'redirect' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::redirect()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function redirect( $url, $status = 302 ) {
		return Response::redirect( response(), $url, $status );
	}
}

if ( ! function_exists( 'reload' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::reload()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function reload( $request, $status = 302 ) {
		return Response::reload( response(), $request, $status );
	}
}

if ( ! function_exists( 'error' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see Response::error()
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	function error( $code ) {
		return Response::error( response(), $code );
	}
}

if ( ! function_exists( 'partial' ) ) {
	/**
	 * @codeCoverageIgnore
	 * @see WPEmerge\View\Php::render()
	 * @return void
	 */
	function partial( $views, $context = [] ) {
		$views = Mixed::toArray( $views );
		$engine = Framework::resolve( WPEMERGE_VIEW_ENGINE_PHP_KEY );
		echo $engine->render( $views, $context );
	}
}
