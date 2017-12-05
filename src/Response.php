<?php

namespace Obsidian;

use Exception;
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
	 * Resolve a template or a template array to an absolute filepath
	 *
	 * @param  string|string[] $templates
	 * @return string
	 */
	protected static function resolveTemplate( $templates ) {
		$templates = is_array( $templates ) ? $templates : [$templates];
		$template = locate_template( $templates, false );

		// locate_template failed to find the template - test if a valid absolute path was passed
		if ( ! $template ) {
			$template = static::resolveTemplateFromFilesystem( $templates );
		}

		return $template;
	}

	/**
	 * Resolve the first existing absolute template filepath from an array of template filepaths
	 *
	 * @param  string[] $templates
	 * @return string
	 */
	protected static function resolveTemplateFromFilesystem( $templates ) {
		foreach ( $templates as $template ) {
			if ( file_exists( $template ) ) {
				return $template;
			}
		}
		return '';
	}

	/**
	 * Get a cloned response, resolving and rendering a template as the body
	 *
	 * @throws Exception
	 * @param  ResponseInterface $response
	 * @param  string|string[]   $templates
	 * @param  array             $context
	 * @return ResponseInterface
	 */
	public static function template( ResponseInterface $response, $templates, $context = array() ) {
		$template = static::resolveTemplate( $templates );

		if ( ! $template ) {
			throw new Exception( 'Could not resolve template.' );
		}

		$engine = Framework::resolve( 'framework.templating.engine' );
		$html = $engine->render( $template, $context );

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
	 * @param  \Obsidian\Request $request
	 * @param  integer           $status
	 * @return ResponseInterface
	 */
	public static function reload( ResponseInterface $response, $request, $status = 302 ) {
		return static::redirect( $response, $request->getUrl(), $status );
	}

	/**
	 * Get a cloned response, with status headers and rendering a suitable template as the body
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
		return static::template( $response, array( $status . '.php', 'index.php' ) );
	}
}
