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
 * A server request representation
 */
class Request {
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
	 * Create a new instance from php super globals.
	 *
	 * @return Request
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
	 * Get the request method.
	 *
	 * @return string
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
	 * Check if the request method is GET.
	 *
	 * @return boolean
	 */
	public function isGet() {
		return $this->getMethod() === 'GET';
	}

	/**
	 * Check if the request method is HEAD.
	 *
	 * @return boolean
	 */
	public function isHead() {
		return $this->getMethod() === 'HEAD';
	}

	/**
	 * Check if the request method is POST.
	 *
	 * @return boolean
	 */
	public function isPost() {
		return $this->getMethod() === 'POST';
	}

	/**
	 * Check if the request method is PUT.
	 *
	 * @return boolean
	 */
	public function isPut() {
		return $this->getMethod() === 'PUT';
	}

	/**
	 * Check if the request method is PATCH.
	 *
	 * @return boolean
	 */
	public function isPatch() {
		return $this->getMethod() === 'PATCH';
	}

	/**
	 * Check if the request method is DELETE.
	 *
	 * @return boolean
	 */
	public function isDelete() {
		return $this->getMethod() === 'DELETE';
	}

	/**
	 * Check if the request method is OPTIONS.
	 *
	 * @return boolean
	 */
	public function isOptions() {
		return $this->getMethod() === 'OPTIONS';
	}

	/**
	 * Check if the request method is a "read" verb.
	 *
	 * @return boolean
	 */
	public function isReadVerb() {
		return in_array( $this->getMethod(), ['GET', 'HEAD', 'OPTIONS'] );
	}

	/**
	 * Get the request url.
	 *
	 * @return string
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
	 * Get a value from any of the request parameters.
	 *
	 * @see    \WPEmerge\Support\Arr
	 * @return mixed
	 */
	protected function input() {
		$args = func_get_args();
		$source = $this->{$args[0]};

		if ( count( $args ) === 1 ) {
			return $source;
		}

		$args[0] = $source;
		return call_user_func_array( [Arr::class, 'get'], $args );
	}

	/**
	 * Get a value from the GET parameters.
	 *
	 * @see    \WPEmerge\Support\Arr
	 * @return mixed
	 */
	public function get() {
		return call_user_func_array( [$this, 'input'], array_merge( ['get'], func_get_args() ) );
	}

	/**
	 * Get a value from the POST parameters.
	 *
	 * @see    \WPEmerge\Support\Arr
	 * @return mixed
	 */
	public function post() {
		return call_user_func_array( [$this, 'input'], array_merge( ['post'], func_get_args() ) );
	}

	/**
	 * Get a value from the COOKIE parameters.
	 *
	 * @see    \WPEmerge\Support\Arr
	 * @return mixed
	 */
	public function cookie() {
		return call_user_func_array( [$this, 'input'], array_merge( ['cookie'], func_get_args() ) );
	}

	/**
	 * Get a value from the FILES parameters.
	 *
	 * @see    \WPEmerge\Support\Arr
	 * @return mixed
	 */
	public function files() {
		return call_user_func_array( [$this, 'input'], array_merge( ['files'], func_get_args() ) );
	}

	/**
	 * Get a value from the SERVER parameters.
	 *
	 * @see    \WPEmerge\Support\Arr
	 * @return mixed
	 */
	public function server() {
		return call_user_func_array( [$this, 'input'], array_merge( ['server'], func_get_args() ) );
	}

	/**
	 * Get a value from the headers.
	 *
	 * @see    \WPEmerge\Support\Arr
	 * @return mixed
	 */
	public function headers() {
		return call_user_func_array( [$this, 'input'], array_merge( ['headers'], func_get_args() ) );
	}
}
