<?php

use Psr\Http\Message\ResponseInterface;
use Obsidian\Response;

/**
 * @coversDefaultClass \Obsidian\Response
 */
class ResponseTest extends WP_UnitTestCase {
    /**
     * @covers ::response
     */
    public function testFromGlobals() {
        $expected = ResponseInterface::class;
        $subject = Response::response();
        $this->assertInstanceOf( $expected, $subject );
    }
}
