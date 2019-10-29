<?php

namespace WPEmergeTests\Middleware;

use Mockery;
use WPEmerge\Middleware\HasMiddlewareTrait;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Middleware\HasMiddlewareTrait
 */
class HasMiddlewareTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = $this->getMockForTrait( HasMiddlewareTrait::class );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::getMiddleware
	 * @covers ::setMiddleware
	 */
	public function testSetMiddleware_EmptyArray_EmptyArray() {
		$expected = [];

		$this->subject->setMiddleware( $expected );
		$this->assertEquals( $expected, $this->subject->getMiddleware() );
	}

	/**
	 * @covers ::setMiddleware
	 */
	public function testSetMiddleware_ValidArray_ValidArray() {
		$expected = ['foo'];

		$this->subject->setMiddleware( $expected );
		$this->assertEquals( $expected, $this->subject->getMiddleware() );
	}

	/**
	 * @covers ::addMiddleware
	 */
	public function testAddMiddleware() {
		$expected = ['foo', 'bar'];

		$this->subject->addMiddleware( 'foo' );
		$this->subject->addMiddleware( 'bar' );

		$this->assertEquals( $expected, $this->subject->getMiddleware() );
	}
}
