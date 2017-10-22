<?php

namespace CarbonFramework;

use CarbonFramework\Support\Arr;

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
		$method = Arr::get( $this->server, 'REQUEST_METHOD', 'GET' );
		
		$override = Arr::get( $this->headers, 'X-HTTP-METHOD-OVERRIDE' );
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
		$host = Arr::get( $this->server, 'HTTP_HOST', '' );
		$uri = Arr::get( $this->server, 'REQUEST_URI', '' );

		return $protocol . '://' . $host . $uri;
	}

	/**
	 * Return a value from any of the request parameters
	 * 
	 * @return string|array
	 */
	protected function input() {
		$args = func_get_args();
		$source = $this->{$args[0]};

		if ( count( $args ) === 1 ) {
			return $source;
		}

		$args[0] = $source;
		return call_user_func_array( [Arr, 'get'], $args );
	}

	/**
	 * Return a value from the GET parameters
	 * 
	 * @return string|array
	 */
	public function get() {
		return call_user_func_array( [$this, 'input'], array_merge( ['get'], func_get_args() ) );
	}

	/**
	 * Return a value from the POST parameters
	 * 
	 * @return string|array
	 */
	public function post() {
		return call_user_func_array( [$this, 'input'], array_merge( ['post'], func_get_args() ) );
	}

	/**
	 * Return a value from the COOKIE parameters
	 * 
	 * @return string|array
	 */
	public function cookie() {
		return call_user_func_array( [$this, 'input'], array_merge( ['cookie'], func_get_args() ) );
	}

	/**
	 * Return a value from the FILES parameters
	 * 
	 * @return string|array
	 */
	public function files() {
		return call_user_func_array( [$this, 'input'], array_merge( ['files'], func_get_args() ) );
	}

	/**
	 * Return a value from the SERVER parameters
	 * 
	 * @return string|array
	 */
	public function server() {
		return call_user_func_array( [$this, 'input'], array_merge( ['server'], func_get_args() ) );
	}

	/**
	 * Return a value from the headers
	 * 
	 * @return string|array
	 */
	public function headers() {
		return call_user_func_array( [$this, 'input'], array_merge( ['headers'], func_get_args() ) );
	}
}
