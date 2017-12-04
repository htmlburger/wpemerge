<?php

use Obsidian\Routing\Conditions\Custom;
use Obsidian\Request;

/**
 * @coversDefaultClass \Obsidian\Routing\Conditions\Custom
 */
class CustomTest extends WP_UnitTestCase {
    /**
     * @covers ::__construct
     * @covers ::getCallable
     * @covers ::getArguments
     */
    public function testConstruct() {
        $callable = function() {};
        $arguments = ['foo', 'bar'];
        $request = Mockery::mock( Request::class )->shouldIgnoreMissing();

        $subject = new Custom( $callable, $arguments[0], $arguments[1] );

        $this->assertSame( $callable, $subject->getCallable() );
        $this->assertEquals( $arguments, $subject->getArguments( $request ) );
    }

    /**
     * @covers ::satisfied
     */
    public function testSatisfied() {
        $request = Mockery::mock( Request::class )->shouldIgnoreMissing();

        $subject1 = new Custom( '__return_true' );
        $this->assertTrue( $subject1->satisfied( $request ) );

        $subject2 = new Custom( '__return_false' );
        $this->assertFalse( $subject2->satisfied( $request ) );
    }
}
