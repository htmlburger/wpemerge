<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Requests;

use GuzzleHttp\Psr7\ServerRequest;
use WPEmerge\Support\Arr;

/**
 * A representation of a request to the server.
 */
class Request extends ServerRequest implements RequestInterface {
	/**
	 * @codeCoverageIgnore
	 * {@inheritDoc}
	 * @return static
	 */
	public static function fromGlobals() {
		$request = parent::fromGlobals();
		$new = new static(
			$request->getMethod(),
			$request->getUri(),
			$request->getHeaders(),
			$request->getBody(),
			$request->getProtocolVersion(),
			$request->getServerParams()
		);

		return $new
			->withCookieParams( $_COOKIE )
			->withQueryParams( $_GET )
			->withParsedBody( $_POST )
			->withUploadedFiles( static::normalizeFiles( $_FILES ) );
	}

	/**
	 * @codeCoverageIgnore
	 * {@inheritDoc}
	 */
	public function getUrl() {
		return $this->getUri();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getMethodOverride( $default ) {
		$valid_overrides = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
		$override = $default;

		$header_override = (string) $this->getHeaderLine( 'X-HTTP-METHOD-OVERRIDE' );
		if ( ! empty( $header_override ) ) {
			$override = strtoupper( $header_override );
		}

		$body_override = (string) $this->body( '_method', '' );
		if ( ! empty( $body_override ) ) {
			$override = strtoupper( $body_override );
		}

		if ( in_array( $override, $valid_overrides, true ) ) {
			return $override;
		}

		return $default;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMethod() {
		$method = parent::getMethod();

		if ( $method === 'POST' ) {
			$method = $this->getMethodOverride( $method );
		}

		return $method;
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
		return strtolower( $this->getHeaderLine( 'X-Requested-With' ) ) === 'xmlhttprequest';
	}

	/**
	 * Get all values or a single one from an input type.
	 *
	 * @param  array $source
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	protected function get( $source, $key = '', $default = null ) {
		if ( empty( $key ) ) {
			return $source;
		}

		return Arr::get( $source, $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::get()
	 */
	public function attributes( $key = '', $default = null ) {
		return call_user_func( [$this, 'get'], $this->getAttributes(), $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::get()
	 */
	public function query( $key = '', $default = null ) {
		return call_user_func( [$this, 'get'], $this->getQueryParams(), $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::get()
	 */
	public function body( $key = '', $default = null ) {
		return call_user_func( [$this, 'get'], $this->getParsedBody(), $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::get()
	 */
	public function cookies( $key = '', $default = null ) {
		return call_user_func( [$this, 'get'], $this->getCookieParams(), $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::get()
	 */
	public function files( $key = '', $default = null ) {
		return call_user_func( [$this, 'get'], $this->getUploadedFiles(), $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::get()
	 */
	public function server( $key = '', $default = null ) {
		return call_user_func( [$this, 'get'], $this->getServerParams(), $key, $default );
	}

	/**
	 * {@inheritDoc}
	 * @see ::get()
	 */
	public function headers( $key = '', $default = null ) {
		return call_user_func( [$this, 'get'], $this->getHeaders(), $key, $default );
	}
}
