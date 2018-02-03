<?php

namespace WPEmergeTests\Input;

use OldInput;
use Mockery;
use WPEmerge\Requests\Request;
use WPEmerge\Input\OldInputMiddleware;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Input\OldInputMiddleware
 */
class OldInputMiddlewareTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->oldInput = OldInput::getFacadeRoot();
		$this->oldInputMock = Mockery::mock()->shouldIgnoreMissing()->asUndefined();
		OldInput::swap( $this->oldInputMock );

		$this->subject = new OldInputMiddleware();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		OldInput::swap( $this->oldInput );
		unset( $this->oldInput );
		unset( $this->oldInputMock );

		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_PostRequest_StoresAll() {
		$post = ['foo' => 'bar'];
		$request = new Request( [], $post, [], [], ['REQUEST_METHOD' => 'POST'], [] );
		$expected = $post;

		$this->oldInputMock->shouldReceive( 'clear' )
			->ordered();
		$this->oldInputMock->shouldReceive( 'store' )
			->with( $post )
			->ordered();

		$this->subject->handle( $request, function() {} );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_GetRequest_DoesNotStore() {
		$post = ['foo' => 'bar'];
		$request = new Request( [], $post, [], [], ['REQUEST_METHOD' => 'GET'], [] );
		$expected = [];

		$this->oldInputMock->shouldReceive( 'clear' )
			->ordered();
		$this->oldInputMock->shouldReceive( 'store' )
			->never()
			->ordered();

		$this->subject->handle( $request, function() {} );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_TwoRequests_ClearsPrevious() {
		$post1 = ['foo' => 'bar'];
		$request1 = new Request( [], $post1, [], [], ['REQUEST_METHOD' => 'POST'], [] );
		$expected1 = $post1;

		$post2 = ['bar' => 'foo'];
		$request2 = new Request( [], $post2, [], [], ['REQUEST_METHOD' => 'GET'], [] );
		$expected2 = [];

		$this->oldInputMock->shouldReceive( 'clear' )
			->ordered(1);
		$this->oldInputMock->shouldReceive( 'store' )
			->with( $post1 )
			->ordered(1);

		$this->oldInputMock->shouldReceive( 'clear' )
			->ordered(2);
		$this->oldInputMock->shouldReceive( 'store' )
			->never()
			->ordered(2);

		$this->subject->handle( $request1, function() {} );
		$this->subject->handle( $request2, function() {} );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_TwoPostRequests_StoresSecondOnly() {
		$post1 = ['foo' => 'bar'];
		$request1 = new Request( [], $post1, [], [], ['REQUEST_METHOD' => 'POST'], [] );
		$expected1 = $post1;

		$post2 = ['bar' => 'foo'];
		$request2 = new Request( [], $post2, [], [], ['REQUEST_METHOD' => 'POST'], [] );
		$expected2 = $post2;

		$this->oldInputMock->shouldReceive( 'clear' )
			->ordered(1);
		$this->oldInputMock->shouldReceive( 'store' )
			->with( $post1 )
			->ordered(1);

		$this->oldInputMock->shouldReceive( 'clear' )
			->ordered(2);
		$this->oldInputMock->shouldReceive( 'store' )
			->with( $post2 )
			->ordered(2);

		$this->subject->handle( $request1, function() {} );
		$this->subject->handle( $request2, function() {} );
		$this->assertTrue( true );
	}
}

class OldInputMiddlewareTestOldInputFacade extends \WPEmerge\Support\Facade {
	protected static function getFacadeAccessor() {
		return 'oldInputMock';
	}
}
