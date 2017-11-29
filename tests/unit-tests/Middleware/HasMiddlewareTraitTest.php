<?php

use Obsidian\Request;
use Obsidian\Middleware\HasMiddlewareTrait;
use Obsidian\Middleware\MiddlewareInterface;
use ObsidianTestTools\TestMiddleware;

/**
 * @coversDefaultClass \Obsidian\Middleware\HasMiddlewareTrait
 */
class HasMiddlewareTraitTest extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();

        $this->subject = $this->getMockForTrait( HasMiddlewareTrait::class );
        $this->middleware_stub1 = $this->getMockBuilder( MiddlewareInterface::class )
            ->getMock();
        $this->middleware_stub2 = $this->getMockBuilder( MiddlewareInterface::class )
            ->getMock();
        $this->request_stub = new Request( [], [], [], [], [], [] );
    }

    public function tearDown() {
        parent::tearDown();

        Mockery::close();
    }

    public static function callableStub() {
        // do nothing
    }

    public static function getClosureMock( $mock, $mock_method ) {
        return function() use ( $mock, $mock_method ) {
            return call_user_func_array( [$mock, $mock_method], func_get_args() );
        };
    }

    /**
     * @covers ::getMiddleware
     * @covers ::addMiddleware
     * @covers ::isMiddleware
     */
    public function testAddMiddleware_MiddlewareInterface_Accepted() {
        $expected = [$this->middleware_stub1];

        $this->subject->addMiddleware( $this->middleware_stub1 );
        $this->assertEquals( $expected, $this->subject->getMiddleware() );
    }

    /**
     * @covers ::getMiddleware
     * @covers ::addMiddleware
     * @covers ::isMiddleware
     */
    public function testAddMiddleware_ArrayOfMiddlewareInterface_Accepted() {
        $expected = [$this->middleware_stub1];

        $this->subject->addMiddleware( $expected );
        $this->assertEquals( $expected, $this->subject->getMiddleware() );
    }

    /**
     * @covers ::getMiddleware
     * @covers ::addMiddleware
     * @covers ::isMiddleware
     */
    public function testAddMiddleware_ArrayOfCallable_Accepted() {
        $callable = [$this, 'callableStub'];
        $expected = [$callable];

        $this->subject->addMiddleware( $expected );
        $this->assertEquals( $expected, $this->subject->getMiddleware() );
    }

    /**
     * @covers ::getMiddleware
     * @covers ::addMiddleware
     * @covers ::isMiddleware
     * @expectedException \Exception
     * @expectedExceptionMessage must be a callable or the name of a class
     */
    public function testAddMiddleware_InvalidMiddleware_ThrowsException() {
        $this->subject->addMiddleware( new stdClass() );
    }

    /**
     * @covers ::getMiddleware
     * @covers ::addMiddleware
     * @covers ::isMiddleware
     */
    public function testAddMiddleware_CalledTwiceWithMiddlewareInterface_MiddlewareMerged() {
        $expected = [$this->middleware_stub1, $this->middleware_stub2];

        $this->subject->addMiddleware( $this->middleware_stub1 );
        $this->subject->addMiddleware( $this->middleware_stub2 );
        $this->assertEquals( $expected, $this->subject->getMiddleware() );
    }

    /**
     * @covers ::executeMiddleware
     */
    public function testExecuteMiddleware_EmptyList_CallsClosureOnce() {
        $mock = Mockery::mock( stdClass::class );
        $method = 'foo';
        $closure = $this->getClosureMock( $mock, $method );

        $mock->shouldReceive( $method )->once();

        $this->subject->executeMiddleware( [], $this->request_stub, $closure );
        $this->assertTrue( true );
    }

    /**
     * @covers ::executeMiddleware
     */
    public function testExecuteMiddleware_OneCallable_CallsCallableFirstThenClosure() {
        $mock = Mockery::mock( stdClass::class );
        $callable = function( $request, $next ) use ( $mock ) {
            call_user_func( $this->getClosureMock( $mock, 'foo' ) );
            return $next( $request );
        };
        $closure = $this->getClosureMock( $mock, 'bar' );

        $mock->shouldReceive( 'foo' )
            ->once()
            ->ordered();

        $mock->shouldReceive( 'bar' )
            ->with( $this->request_stub )
            ->once()
            ->ordered();

        $this->subject->executeMiddleware( [$callable], $this->request_stub, $closure );
        $this->assertTrue( true );
    }

    /**
     * @covers ::executeMiddleware
     */
    public function testExecuteMiddleware_OneMiddlewareInterface_CallsCallableFirstThenClosure() {
        $mock = Mockery::mock( stdClass::class );
        $closure = $this->getClosureMock( $mock, 'bar' );

        $mock->shouldReceive( 'bar' )
            ->with( $this->request_stub )
            ->once()
            ->ordered();

        $this->subject->executeMiddleware( [TestMiddleware::class], $this->request_stub, $closure );
        $this->assertTrue( true );
    }

    /**
     * @covers ::executeMiddleware
     */
    public function testExecuteMiddleware_ThreeCallables_CallsCallablesLastInFirstOutThenClosure() {
        $mock = Mockery::mock( stdClass::class );
        $callable1 = function( $request, $next ) use ( $mock ) {
            call_user_func( $this->getClosureMock( $mock, 'foo' ) );
            return $next( $request );
        };
        $callable2 = function( $request, $next ) use ( $mock ) {
            call_user_func( $this->getClosureMock( $mock, 'bar' ) );
            return $next( $request );
        };
        $callable3 = function( $request, $next ) use ( $mock ) {
            call_user_func( $this->getClosureMock( $mock, 'baz' ) );
            return $next( $request );
        };
        $closure = $this->getClosureMock( $mock, 'foobarbaz' );

        $mock->shouldReceive( 'baz' )
            ->once()
            ->ordered();

        $mock->shouldReceive( 'bar' )
            ->once()
            ->ordered();

        $mock->shouldReceive( 'foo' )
            ->once()
            ->ordered();

        $mock->shouldReceive( 'foobarbaz' )
            ->with( $this->request_stub )
            ->once()
            ->ordered();

        $this->subject->executeMiddleware( [$callable1, $callable2, $callable3], $this->request_stub, $closure );
        $this->assertTrue( true );
    }
}
