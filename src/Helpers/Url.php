<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Helpers;

use WPEmerge\Requests\RequestInterface;

/**
 * A collection of tools dealing with urls
 */
class Url {
	/**
	 * Get the path for the request relative to the home url
	 *
	 * @param  RequestInterface $request
	 * @return string
	 */
	public static function getPath( RequestInterface $request ) {
		$url = $request->getUrl();
		$relative_url = substr( $url, strlen( home_url( '/' ) ) );
		$relative_url = static::addLeadingSlash( $relative_url );
		$relative_url = preg_replace( '~\?.*~', '', $relative_url );
		$relative_url = static::addTrailingSlash( $relative_url );
		return $relative_url;
	}

	/**
	 * Ensure url has a leading slash
	 *
	 * @param  string  $url
	 * @param  boolean $leave_blank
	 * @return string
	 */
	public static function addLeadingSlash( $url, $leave_blank = false ) {
		if ( $leave_blank && $url === '' ) {
			return '';
		}

		return '/' . static::removeLeadingSlash( $url );
	}

	/**
	 * Ensure url does not have a leading slash
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function removeLeadingSlash( $url ) {
		return preg_replace( '/^\/+/', '', $url );
	}

	/**
	 * Ensure url has a trailing slash
	 *
	 * @param  string  $url
	 * @param  boolean $leave_blank
	 * @return string
	 */
	public static function addTrailingSlash( $url, $leave_blank = false ) {
		if ( $leave_blank && $url === '' ) {
			return '';
		}

		return trailingslashit( $url );
	}

	/**
	 * Ensure url does not have a trailing slash
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function removeTrailingSlash( $url ) {
		return untrailingslashit( $url );
	}
}
