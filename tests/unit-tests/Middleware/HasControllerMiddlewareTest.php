<?php

namespace WPEmergeTests\Middleware;

use Mockery;
use WPEmerge\Middleware\HasControllerMiddlewareTrait;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Middleware\HasControllerMiddlewareTrait
 */
class HasControllerMiddlewareTest extends TestCase {
	public function set_up() {
		$this->subject = $this->getMockForTrait( HasControllerMiddlewareTrait::class );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::addMiddleware
	 * @covers ::getMiddleware
	 */
	public function testGetMiddleware() {
		$this->subject->addMiddleware( 'foo' );
		$this->subject->addMiddleware( 'bar' )->only( 'method2' );
		$this->subject->addMiddleware( ['baz'] )->except( 'method3' );

		$this->assertEquals( ['foo', 'baz'], $this->subject->getMiddleware( 'method1' ) );
		$this->assertEquals( ['foo', 'bar', 'baz'], $this->subject->getMiddleware( 'method2' ) );
		$this->assertEquals( ['foo'], $this->subject->getMiddleware( 'method3' ) );
	}
}
