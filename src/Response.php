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
	 * @credit slimphp/slim Slim/App.php
	 * 
	 * @param  ResponseInterface $response
	 * @return null
	 */
	public static function respond( ResponseInterface $response ) {
		// Send response
		if (!headers_sent()) {
			// Status
			header(sprintf(
				'HTTP/%s %s %s',
				$response->getProtocolVersion(),
				$response->getStatusCode(),
				$response->getReasonPhrase()
			));
			// Headers
			foreach ($response->getHeaders() as $name => $values) {
				foreach ($values as $value) {
					header(sprintf('%s: %s', $name, $value), false);
				}
			}
		}
		// Body
		$body = $response->getBody();
		if ($body->isSeekable()) {
			$body->rewind();
		}
		$chunkSize = 4096;
		$contentLength = $response->getHeaderLine('Content-Length');
		if (!$contentLength) {
			$contentLength = $body->getSize();
		}
		if (isset($contentLength)) {
			$amountToRead = $contentLength;
			while ($amountToRead > 0 && !$body->eof()) {
				$data = $body->read(min($chunkSize, $amountToRead));
				echo $data;
				$amountToRead -= strlen($data);
				if (connection_status() != CONNECTION_NORMAL) {
					break;
				}
			}
		} else {
			while (!$body->eof()) {
				echo $body->read($chunkSize);
				if (connection_status() != CONNECTION_NORMAL) {
					break;
				}
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
