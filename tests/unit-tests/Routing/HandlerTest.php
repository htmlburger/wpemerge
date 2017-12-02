<?php

use Obsidian\Routing\Handler;
use Psr\Http\Message\ResponseInterface;

/**
 * @coversDefaultClass \Obsidian\Routing\Handler
 */
class HandlerTest extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();

        $this->subject = new Handler( function() {} );
    }

    public function tearDown() {
        parent::tearDown();
        Mockery::close();

        unset( $this->subject );
    }

    /**
     * @covers ::__construct
     * @covers ::set
     * @covers ::get
     */
    public function testConstruct() {
        $expected = function() {};

        $subject = new Handler( $expected );

        $this->assertSame( $expected, $subject->get() );
    }

    /**
     * @covers ::set
     * @covers ::parse
     */
    public function testSet_Closure_SameClosure() {
        $expected = function() {};

        $this->subject->set( $expected );
        $this->assertSame( $expected, $this->subject->get() );
    }

    /**
     * @covers ::set
     * @covers ::parse
     * @covers ::parseFromString
     */
    public function testSet_ClassAtMethod_Array() {
        $expected = [
            'class' => '\ObsidianTestTools\TestService',
            'method' => 'getTest',
        ];

        $this->subject->set( '\ObsidianTestTools\TestService@getTest' );
        $this->assertEquals( $expected, $this->subject->get() );
    }

    /**
     * @covers ::set
     * @covers ::parse
     * @covers ::parseFromString
     */
    public function testSet_ClassColonsMethod_Array() {
        $expected = [
            'class' => '\ObsidianTestTools\TestService',
            'method' => 'getTest',
        ];

        $this->subject->set( '\ObsidianTestTools\TestService::getTest' );
        $this->assertEquals( $expected, $this->subject->get() );
    }

    /**
     * @covers ::set
     * @covers ::parse
     * @covers ::parseFromString
     * @expectedException \Exception
     * @expectedExceptionMessage No or invalid handler
     */
    public function testSet_InvalidString_ThrowException() {
        $this->subject->set( '\ObsidianTestTools\TestService' );
    }

    /**
     * @covers ::set
     * @covers ::parse
     * @expectedException \Exception
     * @expectedExceptionMessage No or invalid handler
     */
    public function testSet_Object_ThrowException() {
        $this->subject->set( new stdClass() );
    }

    /**
     * @covers ::execute
     * @covers ::executeHandler
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

        $this->subject->set( $closure );
        $this->subject->execute( $mock, $stub );
        $this->assertTrue( true );
    }

    /**
     * @covers ::execute
     * @covers ::executeHandler
     */
    public function testExecute_ClassAtMethod_CalledWithArguments() {
        $foo = 'foo';
        $bar = 'bar';
        $expected = (object) ['value' => $foo . $bar];

        $this->subject->set( 'HandlerTestControllerMock@foobar' );
        $this->assertEquals( $expected, $this->subject->execute( 'foo', 'bar' ) );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_ClosureReturningString_OutputResponse() {
        $expected = 'foobar';
        $closure = function( $value ) {
            return $value;
        };

        $this->subject->set( $closure );
        $response = $this->subject->execute( $expected );
        $this->assertEquals( $expected, $response->getBody()->read( strlen( $expected ) ) );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_ClosureReturningArray_JsonResponse() {
        $value = ['foo' => 'bar'];
        $expected = json_encode( $value );
        $closure = function( $value ) {
            return $value;
        };

        $this->subject->set( $closure );
        $response = $this->subject->execute( $value );
        $this->assertEquals( $expected, $response->getBody()->read( strlen( $expected ) ) );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_ClosureReturningResponse_SameResponse() {
        $expected = Mockery::mock( ResponseInterface::class );
        $closure = function() use ( $expected ) {
            return $expected;
        };

        $this->subject->set( $closure );
        $this->assertSame( $expected, $this->subject->execute() );
    }
}

class HandlerTestControllerMock {
    public function foobar( $foo, $bar ) {
        return (object) ['value' => $foo . $bar];
    }
}
