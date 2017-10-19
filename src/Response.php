<?php

namespace CarbonFramework;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response as Psr7Response;

class Response {
	public static function response() {
		return new Psr7Response();
	}

	public static function echo( $response, $output ) {
		$response = $response->withBody( Psr7\stream_for( $output ) );
		return $response;
	}

	public static function template( $response, $templates, $context = array() ) {
		$templates = (array) $templates;

		$__template = locate_template( $templates, false );
		$__context = $context;
		$renderer = function() use ( $__template, $__context ) {
			ob_start();
			extract( $__context );
			include( $__template );
			return ob_get_clean();
		};
		$html = $renderer();

		$response = $response->withHeader( 'Content-Type', 'text/html' );
		$response = $response->withBody( Psr7\stream_for( $html ) );
		return $response;
	}

	public static function json( $response, $data ) {
		$response = $response->withHeader( 'Content-Type', 'application/json' );
		$response = $response->withBody( Psr7\stream_for( wp_json_encode( $data ) ) );
		return $response;
	}

	public static function redirect( $response, $url, $status = 302 ) {
		$response = $response->withStatus( $status );
		$response = $response->withHeader( 'Location', $url );
		return $response;
	}

	public static function error( $response, $status ) {
		global $wp_query;
		if ( $status === 404 ) {
			$wp_query->set_404();
		}

		$response = $response->withStatus( $status );
		return static::template( $response, array( $status . '.php', 'index.php' ) );
	}
}
