<?php

use CarbonFramework\Url;

class UrlTest extends WP_UnitTestCase {
    public function testAddLeadingSlash() {
        $this->assertEquals( '/example', Url::addLeadingSlash('example') );
        $this->assertEquals( '/example', Url::addLeadingSlash('/example') );
    }

    public function testRemoveLeadingSlash() {
        $this->assertEquals( 'example', Url::removeLeadingSlash('/example') );
        $this->assertEquals( 'example', Url::removeLeadingSlash('example') );
    }

    public function testAddTrailingSlash() {
        $this->assertEquals( 'example/', Url::addTrailingSlash('example') );
        $this->assertEquals( 'example/', Url::addTrailingSlash('example/') );
    }

    public function testRemoveTrailingSlash() {
        $this->assertEquals( 'example', Url::removeTrailingSlash('example/') );
        $this->assertEquals( 'example', Url::removeTrailingSlash('example') );
    }
}
