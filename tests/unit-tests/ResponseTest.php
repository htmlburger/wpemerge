<?php

use Psr\Http\Message\ResponseInterface;
use Obsidian\Response;

/**
 * @coversDefaultClass \Obsidian\Response
 */
class ResponseTest extends WP_UnitTestCase {
    protected function readStream( $stream, $chunk_size = 4096 ) {
        $output = '';
        while ( ! $stream->eof() ) {
            $output .= $stream->read( $chunk_size );
        }
        return $output;
    }

    /**
     * @covers ::response
     */
    public function testResponse() {
        $expected = ResponseInterface::class;

        $subject = Response::response();
        $this->assertInstanceOf( $expected, $subject );
    }

    /**
     * @covers ::output
     */
    public function testOutut() {
        $expected = 'foobar';

        $subject = Response::output( Response::response(), $expected );
        $this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
    }

    /**
     * @covers ::template
     * @covers ::resolveTemplate
     * @covers ::resolveTemplateFromFilesystem
     */
    public function testTemplate() {
        $template = OBSIDIAN_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'template.php';
        $expected = file_get_contents( $template );

        $subject = Response::template( Response::response(), $template );
        $this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
    }

    /**
     * @covers ::template
     * @covers ::resolveTemplate
     * @covers ::resolveTemplateFromFilesystem
     * @expectedException \Exception
     * @expectedExceptionMessage Could not resolve template
     */
    public function testTemplate_NoTemplate() {
        $subject = Response::template( Response::response(), '' );
    }

    /**
     * @covers ::json
     */
    public function testJson() {
        $input = array( 'foo' => 'bar' );
        $expected = json_encode( $input );

        $subject = Response::json( Response::response(), $input );
        $this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
    }

    /**
     * @covers ::redirect
     */
    public function testRedirect_Location() {
        $expected = '/foobar';

        $subject = Response::redirect( Response::response(), $expected );
        $this->assertEquals( $expected, $subject->getHeaderLine( 'Location' ) );
    }

    /**
     * @covers ::redirect
     */
    public function testRedirect_Status() {
        $expected1 = 301;
        $expected2 = 302;

        $subject1 = Response::redirect( Response::response(), 'foobar', $expected1 );
        $this->assertEquals( $expected1, $subject1->getStatusCode() );

        $subject2 = Response::redirect( Response::response(), 'foobar', $expected2 );
        $this->assertEquals( $expected2, $subject2->getStatusCode() );
    }

    /**
     * @covers ::error
     */
    public function testError() {
        $expected1 = 404;
        $expected2 = 500;

        $subject1 = Response::error( Response::response(), $expected1 );
        $this->assertEquals( $expected1, $subject1->getStatusCode() );

        $subject2 = Response::error( Response::response(), $expected2 );
        $this->assertEquals( $expected2, $subject2->getStatusCode() );
    }
}
