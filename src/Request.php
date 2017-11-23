<?php

namespace Obsidian;

use Obsidian\Support\Arr;

/**
 * A server request representation
 */
class Request {
	/**
	 * GET parameters
	 *
	 * @var array
	 */
	protected $get = [];

	/**
	 * POST parameters
	 *
	 * @var array
	 */
	protected $post = [];

	/**
	 * COOKIE parameters
	 *
	 * @var array
	 */
	protected $cookie = [];

	/**
	 * FILES parameters
	 *
	 * @var array
	 */
	protected $files = [];

	/**
	 * SERVER parameters
	 *
	 * @var array
	 */
	protected $server = [];

	/**
	 * Headers
	 *
	 * @var array
	 */
	protected $headers = [];

	/**
	 * Create a new instance from php superglobals
	 *
	 * @return Request
	 */
	public static function fromGlobals() {
		return new static( stripslashes_deep( $_GET ), stripslashes_deep( $_POST ), $_COOKIE, $_FILES, $_SERVER, getallheaders() );
	}

	/**
	 * Constructor
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
	 * Return the request method
	 *
	 * @return string
	 */
	public function getMethod() {
		$method = (string) Arr::get( $this->server, 'REQUEST_METHOD', 'GET' );

		$override = (string) Arr::get( $this->headers, 'X-HTTP-METHOD-OVERRIDE' );
		if ( $method === 'POST' && $override ) {
			$method = $override;
		}

		return strtoupper( $method );
	}

	/**
	 * Return the request url
	 *
	 * @return string
	 */
	public function getUrl() {
		$https = Arr::get( $this->server, 'HTTPS' );

		$protocol = $https ? 'https' : 'http';
		$host = (string) Arr::get( $this->server, 'HTTP_HOST', '' );
		$uri = (string) Arr::get( $this->server, 'REQUEST_URI', '' );
		$uri = Url::addLeadingSlash( $uri );

		return $protocol . '://' . $host . $uri;
	}

	/**
	 * Return a value from any of the request parameters
	 *
	 * @see  \Obsidian\Support\Arr
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
	 * Return a value from the GET parameters
	 *
	 * @see  \Obsidian\Support\Arr
	 * @return mixed
	 */
	public function get() {
		return call_user_func_array( [$this, 'input'], array_merge( ['get'], func_get_args() ) );
	}

	/**
	 * Return a value from the POST parameters
	 *
	 * @see  \Obsidian\Support\Arr
	 * @return mixed
	 */
	public function post() {
		return call_user_func_array( [$this, 'input'], array_merge( ['post'], func_get_args() ) );
	}

	/**
	 * Return a value from the COOKIE parameters
	 *
	 * @see  \Obsidian\Support\Arr
	 * @return mixed
	 */
	public function cookie() {
		return call_user_func_array( [$this, 'input'], array_merge( ['cookie'], func_get_args() ) );
	}

	/**
	 * Return a value from the FILES parameters
	 *
	 * @see  \Obsidian\Support\Arr
	 * @return mixed
	 */
	public function files() {
		return call_user_func_array( [$this, 'input'], array_merge( ['files'], func_get_args() ) );
	}

	/**
	 * Return a value from the SERVER parameters
	 *
	 * @see  \Obsidian\Support\Arr
	 * @return mixed
	 */
	public function server() {
		return call_user_func_array( [$this, 'input'], array_merge( ['server'], func_get_args() ) );
	}

	/**
	 * Return a value from the headers
	 *
	 * @see  \Obsidian\Support\Arr
	 * @return mixed
	 */
	public function headers() {
		return call_user_func_array( [$this, 'input'], array_merge( ['headers'], func_get_args() ) );
	}
}
