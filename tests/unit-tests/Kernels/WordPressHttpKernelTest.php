<?php

namespace WPEmergeTests\Routing;

use Exception;
use Mockery;
use WPEmerge\Application\Application;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Kernels\WordPressHttpKernel;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Router;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Kernels\WordPressHttpKernel
 */
class WordPressHttpKernelTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->app = Mockery::mock( Application::class )->shouldIgnoreMissing();
		$this->router = Mockery::mock( Router::class )->shouldIgnoreMissing();
		$this->error_handler = Mockery::mock( ErrorHandlerInterface::class )->shouldIgnoreMissing();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->app );
		unset( $this->router );
		unset( $this->error_handler );
	}

	/**
	 * @covers ::handle
	 * @expectedException \Exception
	 * @expectedExceptionMessage Test exception handled
	 */
	public function testHandle_Exception_UseErrorHandler() {
		// TODO test error handler unregister
		$exception = new Exception();
		$request = Mockery::mock( RequestInterface::class );
		$subject = new WordPressHttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'execute' )
			->andReturnUsing( function() use ( $exception ) {
				throw $exception;
			} );

		$this->error_handler->shouldReceive( 'getResponse' )
			->with( $exception )
			->andReturnUsing( function() {
				throw new Exception( 'Test exception handled' );
			} );

		$subject->handle( $request, '' );
	}
}
