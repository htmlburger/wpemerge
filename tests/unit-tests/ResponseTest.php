<?php

use Psr\Http\Message\ResponseInterface;
use CarbonFramework\Response;

class ResponseTest extends WP_UnitTestCase {
    /**
     * @covers \CarbonFramework\Response::response
     */
    public function testFromGlobals() {
        $expected = ResponseInterface::class;
        $subject = Response::response();
        $this->assertInstanceOf( $expected, $subject );
    }
}
