<?php

namespace WPEmergeTests\Exceptions;

use Exception;
use Mockery;
use WPEmerge\Exceptions\ExceptionHandler;
use WPEmerge\Exceptions\NotFoundException;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Exceptions\ExceptionHandler
 */
class ExceptionHandlerTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new ExceptionHandler( false, function() {
			throw new Exception( 'Debug stack trace handler called unexpectedly.' );
		} );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_NotFoundException_404Response() {
		$exception = new NotFoundException();
		$expected = 404;

		$this->assertEquals( $expected, $this->subject->handle( $exception )->getStatusCode() );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Debug_StackTraceHandlerCalled() {
		$expected = 'foo';
		$exception = new Exception( 'Rethrown exception' );
		$subject = new ExceptionHandler( true, function() use ( $expected ) {
			return $expected;
		} );

		$this->assertEquals( $expected, $subject->handle( $exception ) );
	}

	/**
	 * @covers ::handle
	 * @expectedException \Exception
	 * @expectedExceptionMessage Rethrown exception
	 */
	public function testHandle_Exception_Rethrow() {
		$exception = new Exception( 'Rethrown exception' );
		$this->subject->handle( $exception );
	}
}
