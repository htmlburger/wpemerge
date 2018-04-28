<?php

namespace WPEmerge\Responses;

use GuzzleHttp\Psr7\Response as Psr7Response;
use WPEmerge\Requests\Request;

/**
 * A collection of tools for the creation of responses
 */
class RedirectResponse extends Psr7Response {
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
		parent::__construct();
		$this->request = $request;
	}

	/**
	 * Get a response redirecting to a specific url.
	 *
	 * @param  string                              $url
	 * @param  integer                             $status
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function to( $url, $status = 302 ) {
		return $this->withHeader( 'Location', $url )->withStatus( $status );
	}

	/**
	 * Get a response redirecting back to the referrer or a fallback.
	 *
	 * @param  string                              $fallback
	 * @param  integer                             $status
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function back( $fallback = '', $status = 302 ) {
		$url = $this->request->headers( 'Referer' );

		if ( empty( $url ) ) {
			$url = $fallback;
		}

		if ( empty( $url ) ) {
			$url = $this->request->getUrl();
		}

		return $this->to( $url, $status );
	}
}



