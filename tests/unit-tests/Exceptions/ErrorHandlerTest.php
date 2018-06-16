<?php

namespace WPEmergeTests\Exceptions;

use Exception;
use Mockery;
use Whoops\RunInterface;
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
		$expected = 404;
		$exception = new NotFoundException();

		$this->assertEquals( $expected, $this->subject->getResponse( $exception )->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 */
	public function testGetResponse_ProductionException_500Response() {
		$expected = 500;
		$exception = new Exception();

		$this->assertEquals( $expected, $this->subject->getResponse( $exception )->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 */
	public function testGetResponse_DebugException_PrettyErrorResponse() {
		$expected = 'foobar';
		$exception = new Exception( $expected );

		$whoops = Mockery::mock( RunInterface::class );
		$whoops->shouldReceive( RunInterface::EXCEPTION_HANDLER )
			->with( $exception )
			->andReturnUsing( function( $exception ) {
				echo $exception->getMessage();
			} );
		$subject = new ErrorHandler( $whoops, true );

		$this->assertEquals( $expected, $subject->getResponse( $exception )->getBody()->read( strlen( $expected ) ) );
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
