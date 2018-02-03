<?php

namespace WPEmergeTests\Middleware;

use Mockery;
use WPEmerge\Requests\Request;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmergeTestTools\TestMiddleware;
use stdClass;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Middleware\HasMiddlewareTrait
 */
class HasMiddlewareTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = $this->getMockForTrait( HasMiddlewareTrait::class );
		$this->middleware_stub1 = Mockery::mock( MiddlewareInterface::class )->shouldIgnoreMissing();
		$this->middleware_stub2 = Mockery::mock( MiddlewareInterface::class )->shouldIgnoreMissing();
		$this->request_stub = new Request( [], [], [], [], [], [] );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	public function getClosureMock( $mock, $mock_method ) {
		return function() use ( $mock, $mock_method ) {
			return call_user_func_array( [$mock, $mock_method], func_get_args() );
		};
	}

	public function callableStub() {
		// do nothing
	}

	/**
	 * @covers ::getMiddleware
	 * @covers ::addMiddleware
	 * @covers ::isMiddleware
	 */
	public function testAddMiddleware_MiddlewareInterfaceClassName_Accepted() {
		$class = TestMiddleware::class;
		$expected = [$class];

		$this->subject->addMiddleware( $class );
		$this->assertEquals( $expected, $this->subject->getMiddleware() );
	}

	/**
	 * @covers ::getMiddleware
	 * @covers ::addMiddleware
	 * @covers ::isMiddleware
	 */
	public function testAddMiddleware_MiddlewareInterfaceInstance_Accepted() {
		$expected = [$this->middleware_stub1];

		$this->subject->addMiddleware( $this->middleware_stub1 );
		$this->assertEquals( $expected, $this->subject->getMiddleware() );
	}

	/**
	 * @covers ::getMiddleware
	 * @covers ::addMiddleware
	 * @covers ::isMiddleware
	 */
	public function testAddMiddleware_ArrayClosure_Accepted() {
		$closure = function() {};
		$expected = [$closure];

		$this->subject->addMiddleware( $closure );
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
	 * @expectedException \Exception
	 * @expectedExceptionMessage must be a closure or the name or instance of a class
	 */
	public function testAddMiddleware_Callable_ThrowsException() {
		$this->assertTrue( is_callable( [$this, 'callableStub'] ) );
		$this->subject->addMiddleware( [$this, 'callableStub'] );
	}

	/**
	 * @covers ::getMiddleware
	 * @covers ::addMiddleware
	 * @covers ::isMiddleware
	 * @expectedException \Exception
	 * @expectedExceptionMessage must be a closure or the name or instance of a class
	 */
	public function testAddMiddleware_InvalidMiddleware_ThrowsException() {
		$this->subject->addMiddleware( new stdClass() );
	}

	/**
	 * @covers ::getMiddleware
	 * @covers ::addMiddleware
	 * @covers ::isMiddleware
	 */
	public function testAddMiddleware_CalledTwiceWithMiddlewareInterfaceInstance_MiddlewareMerged() {
		$expected = [$this->middleware_stub1, $this->middleware_stub2];

		$this->subject->addMiddleware( $this->middleware_stub1 );
		$this->subject->addMiddleware( $this->middleware_stub2 );
		$this->assertEquals( $expected, $this->subject->getMiddleware() );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_EmptyList_CallsClosureOnce() {
		$mock = Mockery::mock();
		$method = 'foo';
		$closure = $this->getClosureMock( $mock, $method );

		$mock->shouldReceive( $method )->once();

		$this->subject->executeMiddleware( [], $this->request_stub, $closure );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_OneClosure_CallsClosureFirstThenClosure() {
		$mock = Mockery::mock();
		$middleware = function( $request, $next ) use ( $mock ) {
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

		$this->subject->executeMiddleware( [$middleware], $this->request_stub, $closure );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_OneMiddlewareInterfaceClassName_CallsClassInstanceFirstThenClosure() {
		$mock = Mockery::mock();
		$closure = $this->getClosureMock( $mock, 'foo' );

		$mock->shouldReceive( 'foo' )
			->with( $this->request_stub )
			->once()
			->ordered();

		$this->subject->executeMiddleware( [TestMiddleware::class], $this->request_stub, $closure );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_OneMiddlewareInterfaceInstance_CallsInstanceFirstThenClosure() {
		$mock = Mockery::mock();
		$closure = $this->getClosureMock( $mock, 'foo' );

		$mock->shouldReceive( 'foo' )
			->with( $this->request_stub )
			->once()
			->ordered();

		$this->subject->executeMiddleware( [new TestMiddleware()], $this->request_stub, $closure );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_ThreeClosures_CallsClosuresLastInFirstOutThenClosure() {
		$mock = Mockery::mock();
		$middleware1 = function( $request, $next ) use ( $mock ) {
			call_user_func( $this->getClosureMock( $mock, 'foo' ) );
			return $next( $request );
		};
		$middleware2 = function( $request, $next ) use ( $mock ) {
			call_user_func( $this->getClosureMock( $mock, 'bar' ) );
			return $next( $request );
		};
		$middleware3 = function( $request, $next ) use ( $mock ) {
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

		$this->subject->executeMiddleware( [$middleware1, $middleware2, $middleware3], $this->request_stub, $closure );
		$this->assertTrue( true );
	}
}
