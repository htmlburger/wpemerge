<?php

use Psr\Http\Message\ResponseInterface;
use CarbonFramework\Response;

/**
 * @coversDefaultClass \CarbonFramework\Response
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
