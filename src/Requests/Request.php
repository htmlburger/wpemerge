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
	public function getUrl() {
		$https = $this->server( 'HTTPS' );

		$protocol = $https ? 'https' : 'http';
		$host = (string) $this->server( 'HTTP_HOST', '' );
		$uri = (string) $this->server( 'REQUEST_URI', '' );
		$uri = Url::addLeadingSlash( $uri );

		return $protocol . '://' . $host . $uri;
	}

	/**
	 * {@inheritDoc}
	 * @see \WPEmerge\Support\Arr
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
	 * {@inheritDoc}
	 * @see \WPEmerge\Support\Arr
	 */
	public function get() {
		return call_user_func_array( [$this, 'input'], array_merge( ['get'], func_get_args() ) );
	}

	/**
	 * {@inheritDoc}
	 * @see \WPEmerge\Support\Arr
	 */
	public function post() {
		return call_user_func_array( [$this, 'input'], array_merge( ['post'], func_get_args() ) );
	}

	/**
	 * {@inheritDoc}
	 * @see \WPEmerge\Support\Arr
	 */
	public function cookie() {
		return call_user_func_array( [$this, 'input'], array_merge( ['cookie'], func_get_args() ) );
	}

	/**
	 * {@inheritDoc}
	 * @see \WPEmerge\Support\Arr
	 */
	public function files() {
		return call_user_func_array( [$this, 'input'], array_merge( ['files'], func_get_args() ) );
	}

	/**
	 * {@inheritDoc}
	 * @see \WPEmerge\Support\Arr
	 */
	public function server() {
		return call_user_func_array( [$this, 'input'], array_merge( ['server'], func_get_args() ) );
	}

	/**
	 * {@inheritDoc}
	 * @see \WPEmerge\Support\Arr
	 */
	public function headers() {
		return call_user_func_array( [$this, 'input'], array_merge( ['headers'], func_get_args() ) );
	}
}
