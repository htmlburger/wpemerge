<?php

namespace CarbonFramework;

class Url {
	public static function getCurrentPath() {
		global $wp;
		return '/' . $wp->request;
	}

	public static function getCurrentUrl() {
		return home_url( add_query_arg( array() ) );
	}

	public static function addLeadingSlash( $url ) {
		return '/' . static::removeLeadingSlash( $url );
	}

	public static function removeLeadingSlash( $url ) {
		return preg_replace( '/^\/+/', '', $url );
	}

	public static function addTrailingSlash( $url ) {
		return trailingslashit( $url );
	}

	public static function removeTrailingSlash( $url ) {
		return untrailingslashit( $url );
	}
}
