<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Requests;

use Psr\Http\Message\ServerRequestInterface;

/**
 * A representation of a request to the server.
 */
interface RequestInterface extends ServerRequestInterface {
	/**
	 * Alias for ::getUri().
	 * Even though URI and URL are slightly different things this alias returns the URI for simplicity/familiarity.
	 *
	 * @return string
	 */
	public function getUrl();

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
	 * Get a value from the request attributes.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function attributes( $key = '', $default = null );

	/**
	 * Get a value from the request query (i.e. $_GET).
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function query( $key = '', $default = null );

	/**
	 * Get a value from the request body (i.e. $_POST).
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function body( $key = '', $default = null );

	/**
	 * Get a value from the COOKIE parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function cookies( $key = '', $default = null );

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
