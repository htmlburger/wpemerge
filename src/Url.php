<?php

namespace CarbonFramework;

use CarbonFramework\Request;

/**
 * A collection of tools dealing with urls
 */
class Url {
	/**
	 * Return the current path relative to the home url
	 * 
	 * @return string
	 */
	public static function getCurrentPath( Request $request ) {
		$url = $request->getUrl();
		$relative_url = substr( $url, strlen( home_url( '/' ) ) );
		return static::addTrailingSlash( static::addLeadingSlash( $relative_url ) );
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
