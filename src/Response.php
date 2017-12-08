<?php

namespace WPEmerge;

use Exception;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response as Psr7Response;
use WPEmerge;
use Psr\Http\Message\ResponseInterface;

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
	 * @credit modified version of slimphp/slim - Slim/App.php
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
	 * @param  ResponseInterface                 $response
	 * @return \Psr\Http\Message\StreamInterface
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

		$content_left = $content_length ? $content_length : -1;
		$amount_to_read = $content_left > -1 ? min( $chunk_size, $content_left ) : $chunk_size;

		while ( ! $body->eof() && $amount_to_read > 0 ) {
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
	 * Resolve a view or a view array to an absolute filepath
	 *
	 * @param  string|string[] $views
	 * @return string
	 */
	protected static function resolveView( $views ) {
		$views = is_array( $views ) ? $views : [$views];
		$view = locate_template( $views, false );

		// locate_template failed to find the view - test if a valid absolute path was passed
		if ( ! $view ) {
			$view = static::resolveViewFromFilesystem( $views );
		}

		return $view;
	}

	/**
	 * Resolve the first existing absolute view filepath from an array of view filepaths
	 *
	 * @param  string[] $views
	 * @return string
	 */
	protected static function resolveViewFromFilesystem( $views ) {
		foreach ( $views as $view ) {
			if ( file_exists( $view ) ) {
				return $view;
			}
		}
		return '';
	}

	/**
	 * Get a cloned response, resolving and rendering a view as the body
	 *
	 * @throws Exception
	 * @param  ResponseInterface $response
	 * @param  string|string[]   $views
	 * @param  array             $context
	 * @return ResponseInterface
	 */
	public static function view( ResponseInterface $response, $views, $context = array() ) {
		$view = static::resolveView( $views );

		if ( ! $view ) {
			throw new Exception( 'Could not resolve view.' );
		}

		$engine = WPEmerge::resolve( WPEMERGE_VIEW_ENGINE_KEY );
		$html = $engine->render( $view, $context );

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
		return static::view( $response, array( $status . '.php', 'index.php' ) );
	}
}
