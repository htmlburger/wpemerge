<?php

namespace WPEmerge;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response as Psr7Response;
use WPEmerge\Facades\Framework;
use WPEmerge\Helpers\Mixed;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A collection of tools for the creation of responses
 */
class Response {
	/**
	 * Create a new response object
	 *
	 * @return ResponseInterface
	 */
	public static function response() {
		return new Psr7Response();
	}

	/**
	 * Send output based on a response object
	 * @credit heavily modified version of slimphp/slim - Slim/App.php
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return void
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
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return void
	 */
	protected static function sendHeaders( ResponseInterface $response ) {
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
	 * Get a response's body stream so it is ready to be read
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return StreamInterface
	 */
	protected static function getBody( ResponseInterface $response ) {
		$body = $response->getBody();
		if ( $body->isSeekable() ) {
			$body->rewind();
		}
		return $body;
	}

	/**
	 * Get a response's body's content length
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return integer
	 */
	protected static function getBodyContentLength( ResponseInterface $response ) {
		$content_length = $response->getHeaderLine( 'Content-Length' );

		if ( ! $content_length ) {
			$body = static::getBody( $response );
			$content_length = $body->getSize();
		}

		if ( ! is_numeric( $content_length ) ) {
			$content_length = 0;
		}

		return (integer) $content_length;
	}

	/**
	 * Send a request's body to the client
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @param  integer           $chunk_size
	 * @return void
	 */
	protected static function sendBody( ResponseInterface $response, $chunk_size = 4096 ) {
		$body = static::getBody( $response );
		$content_length = static::getBodyContentLength( $response );

		if ( $content_length > 0 ) {
			static::sendBodyWithLength( $body, $content_length, $chunk_size );
		} else {
			static::sendBodyWithoutLength( $body, $chunk_size );
		}
	}

	/**
	 * Send a body with an unknown length to the client
	 *
	 * @codeCoverageIgnore
	 * @param  StreamInterface $body
	 * @param  integer         $chunk_size
	 * @return void
	 */
	protected static function sendBodyWithoutLength( StreamInterface $body, $chunk_size ) {
		while ( ! $body->eof() ) {
			echo $body->read( $chunk_size );

			if ( connection_status() != CONNECTION_NORMAL ) {
				break;
			}
		}
	}

	/**
	 * Send a body with a known length to the client
	 *
	 * @codeCoverageIgnore
	 * @param  StreamInterface $body
	 * @param  integer         $length
	 * @param  integer         $chunk_size
	 * @return void
	 */
	protected static function sendBodyWithLength( StreamInterface $body, $length, $chunk_size ) {
		$content_left = $length;

		while ( $content_left > 0 ) {
			$read = min( $chunk_size, $content_left );

			if ( $read <= 0 ) {
				break;
			}

			echo $body->read( $read );

			$content_left -= $read;

			if ( connection_status() != CONNECTION_NORMAL ) {
				break;
			}
		}
	}

	/**
	 * Get a cloned response with the passed string as the body
	 *
	 * @param  ResponseInterface $response
	 * @param  string            $output
	 * @return ResponseInterface
	 */
	public static function output( ResponseInterface $response, $output ) {
		$response = $response->withBody( Psr7\stream_for( $output ) );
		return $response;
	}

	/**
	 * Get a cloned response, resolving and rendering a view as the body
	 *
	 * @param  ResponseInterface $response
	 * @param  string|string[]   $views
	 * @param  array             $context
	 * @return ResponseInterface
	 */
	public static function view( ResponseInterface $response, $views, $context = array() ) {
		$views = Mixed::toArray( $views );
		$engine = Framework::resolve( WPEMERGE_VIEW_ENGINE_KEY );
		$html = $engine->render( $views, $context );

		$response = $response->withHeader( 'Content-Type', 'text/html' );
		$response = $response->withBody( Psr7\stream_for( $html ) );
		return $response;
	}

	/**
	 * Get a cloned response, json encoding the passed data as the body
	 *
	 * @param  ResponseInterface $response
	 * @param  mixed             $data
	 * @return ResponseInterface
	 */
	public static function json( ResponseInterface $response, $data ) {
		$response = $response->withHeader( 'Content-Type', 'application/json' );
		$response = $response->withBody( Psr7\stream_for( wp_json_encode( $data ) ) );
		return $response;
	}

	/**
	 * Get a cloned response, with location and status headers
	 *
	 * @param  ResponseInterface $response
	 * @param  string            $url
	 * @param  integer           $status
	 * @return ResponseInterface
	 */
	public static function redirect( ResponseInterface $response, $url, $status = 302 ) {
		$response = $response->withStatus( $status );
		$response = $response->withHeader( 'Location', $url );
		return $response;
	}

	/**
	 * Get a cloned response, with location header equal to the current url and status header
	 *
	 * @param  ResponseInterface $response
	 * @param  \WPEmerge\Request $request
	 * @param  integer           $status
	 * @return ResponseInterface
	 */
	public static function reload( ResponseInterface $response, $request, $status = 302 ) {
		return static::redirect( $response, $request->getUrl(), $status );
	}

	/**
	 * Get a cloned response, with status headers and rendering a suitable view as the body
	 *
	 * @param  ResponseInterface $response
	 * @param  integer           $status
	 * @return ResponseInterface
	 */
	public static function error( ResponseInterface $response, $status ) {
		global $wp_query;
		if ( $status === 404 ) {
			$wp_query->set_404();
		}

		$response = $response->withStatus( $status );
		return static::view( $response, [$status, 'error', 'index'] );
	}
}
