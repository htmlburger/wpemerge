<?php

use PHPUnit\Framework\TestCase;

use CarbonFramework\Url;

class UrlTest extends TestCase {
	public function testAddLeadingSlash() {
        $this->assertEquals( '/example', Url::addLeadingSlash('example') );
        $this->assertEquals( '/example', Url::addLeadingSlash('/example') );
    }

	public function testRemoveLeadingSlash() {
        $this->assertEquals( 'example', Url::removeLeadingSlash('/example') );
        $this->assertEquals( 'example', Url::removeLeadingSlash('example') );
    }
    /*
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
	*/
}
