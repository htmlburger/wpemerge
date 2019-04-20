<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Requests;

use WPEmerge\Helpers\Url;
use WPEmerge\Support\Arr;

/**
 * A representation of a request to the server.
 */
class Request implements RequestInterface {
	/**
	 * GET parameters.
	 *
	 * @var array
	 */
	protected $get = [];

	/**
	 * POST parameters.
	 *
	 * @var array
	 */
	protected $post = [];

	/**
	 * COOKIE parameters.
	 *
	 * @var array
	 */
	protected $cookie = [];

	/**
	 * FILES parameters.
	 *
	 * @var array
	 */
	protected $files = [];

	/**
	 * SERVER parameters.
	 *
	 * @var array
	 */
	protected $server = [];

	/**
	 * Headers.
	 *
	 * @var array
	 */
	protected $headers = [];

	/**
	 * {@inheritDoc}
	 */
	public static function fromGlobals() {
		return new static(
			stripslashes_deep( $_GET ),
			stripslashes_deep( $_POST ),
			$_COOKIE,
			$_FILES,
			$_SERVER,
			getallheaders()
		);
	}

	/**
	 * Constructor.
	 *
	 * @param array $get
	 * @param array $post
	 * @param array $cookie
	 * @param array $files
	 * @param array $server
	 * @param array $headers
	 */
	public function __construct( $get, $post, $cookie, $files, $server, $headers ) {
		$this->get = $get;
		$this->post = $post;
		$this->cookie = $cookie;
		$this->files = $files;
		$this->server = $server;
		$this->headers = $headers;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMethod() {
		$method = (string) $this->server( 'REQUEST_METHOD', 'GET' );

		$header_override = (string) $this->headers( 'X-HTTP-METHOD-OVERRIDE' );
		if ( $method === 'POST' && $header_override ) {
			$method = strtoupper( $header_override );
		}

		$body_override = (string) $this->post( '_method' );
		if ( $method === 'POST' && $body_override ) {
			$method = strtoupper( $body_override );
		}

		return strtoupper( $method );
	}

	/**
	 * {@inheritDoc}
	 */
	public function isGet() {
		return $this->getMethod() === 'GET';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isHead() {
		return $this->getMethod() === 'HEAD';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPost() {
		return $this->getMethod() === 'POST';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPut() {
		return $this->getMethod() === 'PUT';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPatch() {
		return $this->getMethod() === 'PATCH';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isDelete() {
		return $this->getMethod() === 'DELETE';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isOptions() {
		return $this->getMethod() === 'OPTIONS';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isReadVerb() {
		return in_array( $this->getMethod(), ['GET', 'HEAD', 'OPTIONS'] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAjax() {
		return $this->headers( 'X-Requested-With' ) === 'XMLHttpRequest';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUrl() {
		$https = $this->server( 'HTTPS' );

		$protocol = $https ? 'https' : 'http';
		$host = (string) $this->server( 'HTTP_HOST', '' );
		$uri = (string) $this->server( 'REQUEST_URI', '' );
		$uri = Url::addLeadingSlash( $uri );

		return $protocol . '://' . $host . $uri;
	}

	/**
	 * Get all values or a single one from an input type.
	 *
	 * @param  string $source
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	protected function input( $source, $key = '', $default = null ) {
		$source = isset( $this->{$source} ) && is_array( $this->{$source} ) ? $this->{$source} : [];

		if ( empty( $key ) ) {
			return $source;
		}

		return Arr::get( $source, $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::input()
	 */
	public function get( $key = '', $default = null ) {
		return call_user_func( [$this, 'input'], 'get', $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::input()
	 */
	public function post( $key = '', $default = null ) {
		return call_user_func( [$this, 'input'], 'post', $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::input()
	 */
	public function cookie( $key = '', $default = null ) {
		return call_user_func( [$this, 'input'], 'cookie', $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::input()
	 */
	public function files( $key = '', $default = null ) {
		return call_user_func( [$this, 'input'], 'files', $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::input()
	 */
	public function server( $key = '', $default = null ) {
		return call_user_func( [$this, 'input'], 'server', $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::input()
	 */
	public function headers( $key = '', $default = null ) {
		return call_user_func( [$this, 'input'], 'headers', $key, $default );
	}
}
