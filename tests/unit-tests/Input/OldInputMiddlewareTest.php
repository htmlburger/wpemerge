<?php

use Obsidian\Request;
use Obsidian\Input\OldInput;
use Obsidian\Input\OldInputMiddleware;

/**
 * @coversDefaultClass \Obsidian\Input\OldInputMiddleware
 */
class OldInputMiddlewareTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		session_start();
		$this->subject = new OldInputMiddleware();
		$this->oldInput = new OldInput();
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->subject );
		unset( $this->oldInput );
		session_destroy();
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_PostRequest_StoresAll() {
		$post = ['foo' => 'bar'];
		$request = new Request( [], $post, [], [], ['REQUEST_METHOD' => 'POST'], [] );
		$expected = $post;

		$this->subject->handle( $request, function() {} );
		$this->assertEquals( $expected, $this->oldInput->all() );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_GetRequest_DoesNotStore() {
		$post = ['foo' => 'bar'];
		$request = new Request( [], $post, [], [], ['REQUEST_METHOD' => 'GET'], [] );
		$expected = [];

		$this->subject->handle( $request, function() {} );
		$this->assertEquals( $expected, $this->oldInput->all() );
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

		$this->subject->handle( $request1, function() {} );
		$this->assertEquals( $expected1, $this->oldInput->all() );

		$this->subject->handle( $request2, function() {} );
		$this->assertEquals( $expected2, $this->oldInput->all() );
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

		$this->subject->handle( $request1, function() {} );
		$this->assertEquals( $expected1, $this->oldInput->all() );

		$this->subject->handle( $request2, function() {} );
		$this->assertEquals( $expected2, $this->oldInput->all() );
	}
}
