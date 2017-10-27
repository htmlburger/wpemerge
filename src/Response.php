<?php

namespace CarbonFramework;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;

/**
 * A collection of tools for the creation of responses
 */
class Response {
	/**
	 * Create a new response object
	 *
	 * @return Psr7Response
	 */
	public static function response() {
		return new Psr7Response();
	}

	/**
	 * Send output based on a response object
	 * @credit modified version of slimphp/slim - Slim/App.php
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return null
	 */
	public static function respond( ResponseInterface $response ) {
		if ( ! headers_sent() ) {
			static::sendHeaders( $response );
		}
		static::sendBody( $response );
	}

	/**
	 * Send a request's headers to the client
	 *
	 * @param  ResponseInterface $response
	 * @return null
	 */
	protected static function sendHeaders( $response ) {
		// Status
		header( sprintf(
			'HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		) );

		// Headers
		foreach ( $response->getHeaders() as $name => $values ) {
			foreach ( $values as $value ) {
				header( sprintf( '%s: %s', $name, $value ), false );
			}
		}
	}

	/**
	 * Send a request's body to the client
	 *
	 * @param  ResponseInterface $response
	 * @return null
	 */
	protected static function sendBody( $response, $chunk_size = 4096 ) {
		$body = $response->getBody();
		if ( $body->isSeekable() ) {
			$body->rewind();
		}

		$content_length = $response->getHeaderLine( 'Content-Length' );
		if ( ! $content_length ) {
			$content_length = $body->getSize();
		}

		$content_left = $content_length ? $content_length : -1;
		$amount_to_read = $content_left > -1 ? min( $chunk_size, $content_left ) : $chunk_size;
		while ( ! $body->eof() ) {
			echo $body->read( $amount_to_read );

			if ( $content_left > -1 ) {
				$content_left -= $amount_to_read;
			}

			if ( connection_status() != CONNECTION_NORMAL ) {
				break;
			}
		}
	}

	/**
	 * Return a cloned response with the passed string as the body
	 *
	 * @param  Psr7Response $response
	 * @param  string       $output
	 * @return Psr7Response
	 */
	public static function output( Psr7Response $response, $output ) {
		$response = $response->withBody( Psr7\stream_for( $output ) );
		return $response;
	}

	/**
	 * Return a cloned response, resolving and rendering a template as the body
	 *
	 * @param  Psr7Response    $response
	 * @param  string|string[] $templates
	 * @param  array           $context
	 * @return Psr7Response
	 */
	public static function template( Psr7Response $response, $templates, $context = array() ) {
		$templates = is_array( $templates ) ? $templates : [$templates];
		$template = locate_template( $templates, false );

		$engine = Framework::resolve( 'framework.templating.engine' );
		$html = $engine->render( $template, $context );

		$response = $response->withHeader( 'Content-Type', 'text/html' );
		$response = $response->withBody( Psr7\stream_for( $html ) );
		return $response;
	}

	/**
	 * Return a cloned response, json encoding the passed array as the body
	 *
	 * @param  Psr7Response $response
	 * @param  array        $data
	 * @return Psr7Response
	 */
	public static function json( Psr7Response $response, $data ) {
		$response = $response->withHeader( 'Content-Type', 'application/json' );
		$response = $response->withBody( Psr7\stream_for( wp_json_encode( $data ) ) );
		return $response;
	}

	/**
	 * Return a cloned response, with location and status headers
	 *
	 * @param  Psr7Response $response
	 * @param  string       $url
	 * @param  integer      $status
	 * @return Psr7Response
	 */
	public static function redirect( Psr7Response $response, $url, $status = 302 ) {
		$response = $response->withStatus( $status );
		$response = $response->withHeader( 'Location', $url );
		return $response;
	}

	/**
	 * Return a cloned response, with status headers and rendering a suitable template as the body
	 *
	 * @param  Psr7Response $response
	 * @param  integer      $status
	 * @return Psr7Response
	 */
	public static function error( Psr7Response $response, $status ) {
		global $wp_query;
		if ( $status === 404 ) {
			$wp_query->set_404();
		}

		$response = $response->withStatus( $status );
		return static::template( $response, array( $status . '.php', 'index.php' ) );
	}
}
