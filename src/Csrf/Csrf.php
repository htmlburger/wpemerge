<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Csrf;

use WPEmerge\Requests\RequestInterface;

/**
 * Provide CSRF protection utilities through WordPress nonces.
 */
class Csrf {
	/**
	 * Convenience header to check for the token.
	 *
	 * @var string
	 */
	protected $header = 'X-CSRF-TOKEN';

	/**
	 * GET/POST parameter key to check for the token.
	 *
	 * @var string
	 */
	protected $key = '';

	/**
	 * Maximum token lifetime.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
	 * @var integer
	 */
	protected $maximum_lifetime = 2;

	/**
	 * Last generated tokens.
	 *
	 * @var array<string, string>
	 */
	protected $tokens = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param string  $key
	 * @param integer $maximum_lifetime
	 */
	public function __construct( $key = '__wpemergeCsrfToken', $maximum_lifetime = 2 ) {
		$this->key = $key;
		$this->maximum_lifetime = $maximum_lifetime;
	}

	/**
	 * Get the last generated token.
	 *
	 * @param  int|string $action
	 * @return string
	 */
	public function getToken( $action = -1 ) {
		if ( ! isset( $this->tokens[ $action ] ) ) {
			return $this->generateToken( $action );
		}

		return $this->tokens[ $action ];
	}

	/**
	 * Get the csrf token from a request.
	 *
	 * @param  RequestInterface $request
	 * @return string
	 */
	public function getTokenFromRequest( RequestInterface $request ) {
		$query = $request->query( $this->key );
		if ( $query ) {
			return $query;
		}

		$body = $request->body( $this->key );
		if ( $body ) {
			return $body;
		}

		$header = $request->getHeaderLine( $this->header );
		if ( $header ) {
			return $header;
		}

		return '';
	}

	/**
	 * Generate a new token.
	 *
	 * @param  int|string $action
	 * @return string
	 */
	public function generateToken( $action = -1 ) {
		$action = $action === -1 ? session_id() : $action;

		$this->tokens[ $action ] = wp_create_nonce( $action );

		return $this->getToken( $action );
	}

	/**
	 * Check if a token is valid.
	 *
	 * @param  string     $token
	 * @param  int|string $action
	 * @return boolean
	 */
	public function isValidToken( $token, $action = -1 ) {
		$action = $action === -1 ? session_id() : $action;
		$lifetime = (int) wp_verify_nonce( $token, $action );

		return ( $lifetime > 0 && $lifetime <= $this->maximum_lifetime );
	}

	/**
	 * Add the token to a URL.
	 *
	 * @param  string     $url
	 * @param  int|string $action
	 * @return string
	 */
	public function url( $url, $action = -1 ) {
		return add_query_arg( $this->key, $this->getToken( $action ), $url );
	}

	/**
	 * Return the markup for a hidden input which holds the current token.
	 *
	 * @param  int|string $action
	 * @return void
	 */
	public function field( $action = -1 ) {
		echo sprintf(
			'<input type="hidden" name="%1$s" value="%2$s" />',
			esc_attr( $this->key ),
			esc_attr( $this->getToken( $action ) )
		);
	}
}
