<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Requests;

/**
 * A representation of a request to the server.
 */
interface RequestInterface {
	/**
	 * Create a new instance from php super globals.
	 *
	 * @return RequestInterface
	 */
	public static function fromGlobals();

	/**
	 * Get the request method.
	 *
	 * @return string
	 */
	public function getMethod();

	/**
	 * Check if the request method is GET.
	 *
	 * @return boolean
	 */
	public function isGet();

	/**
	 * Check if the request method is HEAD.
	 *
	 * @return boolean
	 */
	public function isHead();

	/**
	 * Check if the request method is POST.
	 *
	 * @return boolean
	 */
	public function isPost();

	/**
	 * Check if the request method is PUT.
	 *
	 * @return boolean
	 */
	public function isPut();

	/**
	 * Check if the request method is PATCH.
	 *
	 * @return boolean
	 */
	public function isPatch();

	/**
	 * Check if the request method is DELETE.
	 *
	 * @return boolean
	 */
	public function isDelete();

	/**
	 * Check if the request method is OPTIONS.
	 *
	 * @return boolean
	 */
	public function isOptions();

	/**
	 * Check if the request method is a "read" verb.
	 *
	 * @return boolean
	 */
	public function isReadVerb();

	/**
	 * Check if the request is an ajax request.
	 *
	 * @return boolean
	 */
	public function isAjax();

	/**
	 * Get the request url.
	 *
	 * @return string
	 */
	public function getUrl();

	/**
	 * Get a value from the GET parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get( $key = '', $default = null );

	/**
	 * Get a value from the POST parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function post( $key = '', $default = null );

	/**
	 * Get a value from the COOKIE parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function cookie( $key = '', $default = null );

	/**
	 * Get a value from the FILES parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function files( $key = '', $default = null );

	/**
	 * Get a value from the SERVER parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function server( $key = '', $default = null );

	/**
	 * Get a value from the headers.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function headers( $key = '', $default = null );
}
