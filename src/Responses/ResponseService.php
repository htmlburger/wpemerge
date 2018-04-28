<?php

namespace WPEmerge\Responses;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response as Psr7Response;
use WPEmerge\Facades\Framework;
use WPEmerge\Facades\View;
use WPEmerge\Helpers\MixedType;
use WPEmerge\Requests\Request;
use WPEmerge\Responses\RedirectResponse;
use WPEmerge\View\ViewInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A collection of tools for the creation of responses.
 */
class ResponseService {
	/**
	 * Current request.
	 *
	 * @var Request
	 */
	protected $request = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Request $request
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Send output based on a response object.
	 * @credit heavily modified version of slimphp/slim - Slim/App.php
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return void
	 */
	public function respond( ResponseInterface $response ) {
		if ( ! headers_sent() ) {
			$this->sendHeaders( $response );
		}
		$this->sendBody( $response );
	}

	/**
	 * Send a request's headers to the client.
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return void
	 */
	protected function sendHeaders( ResponseInterface $response ) {
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
	 * Get a response's body stream so it is ready to be read.
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return StreamInterface
	 */
	protected function getBody( ResponseInterface $response ) {
		$body = $response->getBody();
		if ( $body->isSeekable() ) {
			$body->rewind();
		}
		return $body;
	}

	/**
	 * Get a response's body's content length.
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @return integer
	 */
	protected function getBodyContentLength( ResponseInterface $response ) {
		$content_length = $response->getHeaderLine( 'Content-Length' );

		if ( ! $content_length ) {
			$body = $this->getBody( $response );
			$content_length = $body->getSize();
		}

		if ( ! is_numeric( $content_length ) ) {
			$content_length = 0;
		}

		return (integer) $content_length;
	}

	/**
	 * Send a request's body to the client.
	 *
	 * @codeCoverageIgnore
	 * @param  ResponseInterface $response
	 * @param  integer           $chunk_size
	 * @return void
	 */
	protected function sendBody( ResponseInterface $response, $chunk_size = 4096 ) {
		$body = $this->getBody( $response );
		$content_length = $this->getBodyContentLength( $response );

		if ( $content_length > 0 ) {
			$this->sendBodyWithLength( $body, $content_length, $chunk_size );
		} else {
			$this->sendBodyWithoutLength( $body, $chunk_size );
		}
	}

	/**
	 * Send a body with an unknown length to the client.
	 *
	 * @codeCoverageIgnore
	 * @param  StreamInterface $body
	 * @param  integer         $chunk_size
	 * @return void
	 */
	protected function sendBodyWithoutLength( StreamInterface $body, $chunk_size ) {
		while ( ! $body->eof() ) {
			echo $body->read( $chunk_size );

			if ( connection_status() != CONNECTION_NORMAL ) {
				break;
			}
		}
	}

	/**
	 * Send a body with a known length to the client.
	 *
	 * @codeCoverageIgnore
	 * @param  StreamInterface $body
	 * @param  integer         $length
	 * @param  integer         $chunk_size
	 * @return void
	 */
	protected function sendBodyWithLength( StreamInterface $body, $length, $chunk_size ) {
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
	 * Create a new response object.
	 *
	 * @return ResponseInterface
	 */
	public function response() {
		return new Psr7Response();
	}

	/**
	 * Get a cloned response with the passed string as the body.
	 *
	 * @param  string            $output
	 * @return ResponseInterface
	 */
	public function output( $output ) {
		$response = $this->response();
		$response = $response->withBody( Psr7\stream_for( $output ) );
		return $response;
	}

	/**
	 * Get a cloned response, json encoding the passed data as the body.
	 *
	 * @param  mixed             $data
	 * @return ResponseInterface
	 */
	public function json( $data ) {
		$response = $this->response();
		$response = $response->withHeader( 'Content-Type', 'application/json' );
		$response = $response->withBody( Psr7\stream_for( wp_json_encode( $data ) ) );
		return $response;
	}

	/**
	 * Get a cloned response, with location and status headers.
	 *
	 * @return RedirectResponse
	 */
	public function redirect() {
		return new RedirectResponse( $this->request );
	}

	/**
	 * Get a view convertible to a response.
	 *
	 * @param  string|string[] $views
	 * @return ViewInterface
	 */
	public function view( $views ) {
		return View::make( $views );
	}

	/**
	 * Get an error response, with status headers and rendering a suitable view as the body.
	 *
	 * @param  integer           $status
	 * @return ResponseInterface
	 */
	public function error( $status ) {
		global $wp_query;
		if ( $status === 404 ) {
			$wp_query->set_404();
		}

		return $this->view( [$status, 'error', 'index'] )
			->toResponse()
			->withStatus( $status );
	}
}
