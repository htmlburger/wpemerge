<?php

namespace WPEmerge\Helpers;

use WPEmerge\Requests\Request;

/**
 * A collection of tools dealing with urls
 */
class Url {
	/**
	 * Get the path for the request relative to the home url
	 *
	 * @param  Request $request
	 * @return string
	 */
	public static function getPath( Request $request ) {
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
	 * @param  string $url
	 * @return string
	 */
	public static function addLeadingSlash( $url ) {
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
	 * @param  string $url
	 * @return string
	 */
	public static function addTrailingSlash( $url ) {
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
