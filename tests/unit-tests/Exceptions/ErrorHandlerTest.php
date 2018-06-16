<?php

namespace WPEmergeTests\Exceptions;

use Exception;
use Mockery;
use WPEmerge\Exceptions\ErrorHandler;
use WPEmerge\Exceptions\NotFoundException;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Exceptions\ErrorHandler
 */
class ErrorHandlerTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new ErrorHandler( null, false );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::getResponse
	 * @covers ::toResponse
	 */
	public function testGetResponse_NotFoundException_404Response() {
		$exception = new NotFoundException();
		$expected = 404;

		$this->assertEquals( $expected, $this->subject->getResponse( $exception )->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 */
	public function testGetResponse_ProductionException_500Response() {
		$exception = new Exception();
		$expected = 500;

		$this->assertEquals( $expected, $this->subject->getResponse( $exception )->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 * @covers ::toResponse
	 * @expectedException \Exception
	 * @expectedExceptionMessage Rethrown exception
	 */
	public function testGetResponse_DebugException_RethrowException() {
		$exception = new Exception( 'Rethrown exception' );
		$subject = new ErrorHandler( null, true );
		$subject->getResponse( $exception );
	}
}
