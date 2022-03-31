<?php

namespace WPEmergeTests\Middleware;

use Mockery;
use WPEmerge\Helpers\Handler;
use WPEmerge\Middleware\HasControllerMiddlewareInterface;
use WPEmerge\Middleware\ReadsHandlerMiddlewareTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Middleware\ReadsHandlerMiddlewareTrait
 */
class ReadsHandlerMiddlewareTraitTest extends TestCase {
	public function set_up() {
		$this->subject = new ReadsHandlerMiddlewareTraitImplementation();
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::getHandlerMiddleware
	 */
	public function testGetHandlerMiddleware_NonControllerMiddlewareHandler_EmptyArray() {
		$handler = Mockery::mock( Handler::class );

		$handler->shouldReceive( 'make' )
			->andReturn( function () {} );

		$this->assertEquals( [], $this->subject->publicGetHandlerMiddleware( $handler ) );
	}

	/**
	 * @covers ::getHandlerMiddleware
	 */
	public function testGetHandlerMiddleware_ControllerMiddlewareHandler_FullArray() {
		$handler = Mockery::mock( Handler::class );
		$instance = Mockery::mock( HasControllerMiddlewareInterface::class );

		$handler->shouldReceive( 'make' )
			->andReturn( $instance );

		$handler->shouldReceive( 'get' )
			->andReturn( ['method' => 'method1'] );

		$instance->shouldReceive( 'getMiddleware' )
			->with( 'method1' )
			->andReturn( ['middleware1'] );

		$this->assertEquals( ['middleware1'], $this->subject->publicGetHandlerMiddleware( $handler ) );
	}
}

class ReadsHandlerMiddlewareTraitImplementation {
	use ReadsHandlerMiddlewareTrait;

	public function publicGetHandlerMiddleware() {
		return call_user_func_array( [$this, 'getHandlerMiddleware'], func_get_args() );
	}
}
