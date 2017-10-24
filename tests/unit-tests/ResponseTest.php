<?php

use GuzzleHttp\Psr7\Response as Psr7Response;
use CarbonFramework\Response;

class ResponseTest extends WP_UnitTestCase {
    /**
     * @covers \CarbonFramework\Response::response
     */
    public function testFromGlobals() {
        $expected = Psr7Response::class;
        $subject = Response::response();
        $this->assertInstanceOf( $expected, $subject );
    }
}
