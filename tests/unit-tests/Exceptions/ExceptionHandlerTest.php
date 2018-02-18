<?php

namespace WPEmergeTests\Exceptions;

use Exception;
use Mockery;
use WPEmerge\Exceptions\ExceptionHandler;
use WPEmerge\Exceptions\NotFoundException;
use WPEmerge\Facades\Response;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Flash\Flash
 */
class ExceptionHandlerTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new ExceptionHandler();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		Response::clearResolvedInstance( WPEMERGE_EXCEPTIONS_EXCEPTION_HANDLER_KEY );

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
	 * @expectedException \Exception
	 * @expectedExceptionMessage Rethrown exception
	 */
	public function testHandle_Exception_Rethrow() {
		$exception = new Exception( 'Rethrown exception' );
		$this->subject->handle( $exception );
	}

}
