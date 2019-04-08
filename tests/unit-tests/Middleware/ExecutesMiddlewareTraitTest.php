<?php

namespace WPEmergeTests\Middleware;

use Mockery;
use WPEmerge\Requests\Request;
use WPEmerge\Middleware\ExecutesMiddlewareTrait;
use WPEmergeTestTools\TestMiddleware;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Middleware\ExecutesMiddlewareTrait
 */
class ExecutesMiddlewareTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = $this->getMockForTrait( ExecutesMiddlewareTrait::class );
		$this->request = new Request( [], [], [], [], [], [] );
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

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_EmptyList_CallsClosureOnce() {
		$mock = Mockery::mock();
		$method = 'foo';
		$closure = $this->getClosureMock( $mock, $method );

		$mock->shouldReceive( $method )->once();

		$this->subject->executeMiddleware( [], $this->request, $closure );
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
			->with( $this->request )
			->once()
			->ordered();

		$this->subject->executeMiddleware( [$middleware], $this->request, $closure );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_OneMiddlewareInterfaceClassName_CallsClassInstanceFirstThenClosure() {
		$mock = Mockery::mock();
		$closure = $this->getClosureMock( $mock, 'foo' );

		$mock->shouldReceive( 'foo' )
			->with( $this->request )
			->once()
			->ordered();

		$this->subject->executeMiddleware( [TestMiddleware::class], $this->request, $closure );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_OneMiddlewareInterfaceInstance_CallsInstanceFirstThenClosure() {
		$mock = Mockery::mock();
		$closure = $this->getClosureMock( $mock, 'foo' );

		$mock->shouldReceive( 'foo' )
			->with( $this->request )
			->once()
			->ordered();

		$this->subject->executeMiddleware( [new TestMiddleware()], $this->request, $closure );
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

		$mock->shouldReceive( 'foo' )
			->once()
			->ordered();

		$mock->shouldReceive( 'bar' )
			->once()
			->ordered();

		$mock->shouldReceive( 'baz' )
			->once()
			->ordered();

		$mock->shouldReceive( 'foobarbaz' )
			->with( $this->request )
			->once()
			->ordered();

		$this->subject->executeMiddleware( [$middleware1, $middleware2, $middleware3], $this->request, $closure );
		$this->assertTrue( true );
	}
}
