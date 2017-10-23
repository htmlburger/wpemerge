<?php

use CarbonFramework\Url as Subject;

class UrlTest extends WP_UnitTestCase {
    /**
     * @covers Subject::getCurrentPath
     */
    public function testGetCurrentPath_Home_Slash() {
        $expected = '/';

        $mock_request = $this->createMock( CarbonFramework\Request::class );
        $mock_request->method( 'getUrl' )->willReturn( 'http://example.org/' );

        $this->assertEquals( $expected, Subject::getCurrentPath( $mock_request ) );
    }

    /**
     * @covers Subject::getCurrentPath
     */
    public function testGetCurrentPath_Subpage_RelativePath() {
        $expected = '/foo/bar/';

        $mock_request = $this->createMock( CarbonFramework\Request::class );
        $mock_request->method( 'getUrl' )->willReturn( 'http://example.org/foo/bar/' );

        $this->assertEquals( $expected, Subject::getCurrentPath( $mock_request ) );
    }

    /**
     * @covers Subject::addLeadingSlash
     */
    public function testAddLeadingSlash() {
        $this->assertEquals( '/example', Subject::addLeadingSlash('example') );
        $this->assertEquals( '/example', Subject::addLeadingSlash('/example') );
    }

    /**
     * @covers Subject::removeLeadingSlash
     */
    public function testRemoveLeadingSlash() {
        $this->assertEquals( 'example', Subject::removeLeadingSlash('/example') );
        $this->assertEquals( 'example', Subject::removeLeadingSlash('example') );
    }

    /**
     * @covers Subject::addTrailingSlash
     */
    public function testAddTrailingSlash() {
        $this->assertEquals( 'example/', Subject::addTrailingSlash('example') );
        $this->assertEquals( 'example/', Subject::addTrailingSlash('example/') );
    }

    /**
     * @covers Subject::removeTrailingSlash
     */
    public function testRemoveTrailingSlash() {
        $this->assertEquals( 'example', Subject::removeTrailingSlash('example/') );
        $this->assertEquals( 'example', Subject::removeTrailingSlash('example') );
    }
}
