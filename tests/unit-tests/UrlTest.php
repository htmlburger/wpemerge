<?php

use CarbonFramework\Url;

/**
 * @coversDefaultClass \CarbonFramework\Url
 */
class UrlTest extends WP_UnitTestCase {
    /**
     * @covers ::getCurrentPath
     */
    public function testGetCurrentPath_Home_Slash() {
        $expected = '/';

        $mock_request = $this->getMockBuilder( CarbonFramework\Request::class )->disableOriginalConstructor()->getMock();
        $mock_request->method( 'getUrl' )->willReturn( 'http://example.org/' );

        $this->assertEquals( $expected, Url::getCurrentPath( $mock_request ) );
    }

    /**
     * @covers ::getCurrentPath
     */
    public function testGetCurrentPath_Subpage_RelativePath() {
        $expected = '/foo/bar/';

        $mock_request = $this->getMockBuilder( CarbonFramework\Request::class )->disableOriginalConstructor()->getMock();
        $mock_request->method( 'getUrl' )->willReturn( 'http://example.org/foo/bar/' );

        $this->assertEquals( $expected, Url::getCurrentPath( $mock_request ) );
    }

    /**
     * @covers ::getCurrentPath
     */
    public function testGetCurrentPath_QueryString_StripsQueryString() {
        $expected = '/foo/bar/';

        $mock_request = $this->getMockBuilder( CarbonFramework\Request::class )->disableOriginalConstructor()->getMock();
        $mock_request->method( 'getUrl' )->willReturn( 'http://example.org/foo/bar/?foo=bar&baz=foobarbaz' );

        $this->assertEquals( $expected, Url::getCurrentPath( $mock_request ) );
    }

    /**
     * @covers ::addLeadingSlash
     */
    public function testAddLeadingSlash() {
        $this->assertEquals( '/example', Url::addLeadingSlash('example') );
        $this->assertEquals( '/example', Url::addLeadingSlash('/example') );
    }

    /**
     * @covers ::removeLeadingSlash
     */
    public function testRemoveLeadingSlash() {
        $this->assertEquals( 'example', Url::removeLeadingSlash('/example') );
        $this->assertEquals( 'example', Url::removeLeadingSlash('example') );
    }

    /**
     * @covers ::addTrailingSlash
     */
    public function testAddTrailingSlash() {
        $this->assertEquals( 'example/', Url::addTrailingSlash('example') );
        $this->assertEquals( 'example/', Url::addTrailingSlash('example/') );
    }

    /**
     * @covers ::removeTrailingSlash
     */
    public function testRemoveTrailingSlash() {
        $this->assertEquals( 'example', Url::removeTrailingSlash('example/') );
        $this->assertEquals( 'example', Url::removeTrailingSlash('example') );
    }
}
