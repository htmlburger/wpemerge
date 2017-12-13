<?php

namespace WPEmergeTests\Helpers;

use Mockery;
use WPEmerge\Helpers\Handler;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Helpers\Handler
 */
class HandlerTest extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testConstruct() {
        $expected = function() {};

        $subject = new Handler( $expected );

        $this->assertSame( $expected, $subject->get() );
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     */
    public function testSet_Closure_Closure() {
        $expected = function() {};

        $subject = new Handler( $expected );

        $this->assertEquals( $expected, $subject->get() );
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     * @expectedException \Exception
     * @expectedExceptionMessage No or invalid handler
     */
    public function testSet_Invalid_ThrowException() {
        $subject = new Handler( new stdClass() );
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     * @covers ::parseFromString
     */
    public function testSet_ClassAtMethod_Array() {
        $expected = [
            'class' => '\WPEmergeTestTools\TestService',
            'method' => 'getTest',
        ];

        $subject = new Handler( '\WPEmergeTestTools\TestService@getTest' );

        $this->assertEquals( $expected, $subject->get() );
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     * @covers ::parseFromString
     */
    public function testSet_ClassColonsMethod_Array() {
        $expected = [
            'class' => '\WPEmergeTestTools\TestService',
            'method' => 'getTest',
        ];

        $subject = new Handler( '\WPEmergeTestTools\TestService::getTest' );

        $this->assertEquals( $expected, $subject->get() );
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     * @covers ::parseFromString
     * @expectedException \Exception
     * @expectedExceptionMessage No or invalid handler
     */
    public function testSet_InvalidString_ThrowException() {
        $subject = new Handler( '\WPEmergeTestTools\TestService' );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_Closure_CalledWithArguments() {
        $stub = new stdClass();
        $mock = Mockery::mock();
        $mock->shouldReceive( 'execute' )
           ->once()
           ->with( $mock, $stub );

        $closure = function( $mock, $stub ) {
            $mock->execute( $mock, $stub );
        };

        $subject = new Handler( $closure );
        $subject->execute( $mock, $stub );
        $this->assertTrue( true );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_ClassAtMethod_CalledWithArguments() {
        $foo = 'foo';
        $bar = 'bar';
        $expected = (object) ['value' => $foo . $bar];

        $subject = new Handler( HandlerTestControllerMock::class . '@foobar' );
        $this->assertEquals( $expected, $subject->execute( 'foo', 'bar' ) );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_ClosureReturningValue_ReturnSameValue() {
        $expected = new stdClass();
        $closure = function( $value ) {
            return $value;
        };

        $subject = new Handler( $closure );
        $this->assertSame( $expected, $subject->execute( $expected ) );
    }
}

class HandlerTestControllerMock {
    public function foobar( $foo, $bar ) {
        return (object) ['value' => $foo . $bar];
    }
}
