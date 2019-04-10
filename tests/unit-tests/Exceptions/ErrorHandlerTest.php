<?php

namespace WPEmergeTests\Exceptions;

use Exception;
use Mockery;
use Whoops\RunInterface;
use WPEmerge\Exceptions\ErrorHandler;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\NotFoundException;
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
		$request = Mockery::mock( RequestInterface::class );

		$this->assertEquals( $expected, $this->subject->getResponse( $request, $exception )->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 */
	public function testGetResponse_ProductionException_500Response() {
		$expected = 500;
		$exception = new Exception();
		$request = Mockery::mock( RequestInterface::class );

		$this->assertEquals( $expected, $this->subject->getResponse( $request, $exception )->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 */
	public function testGetResponse_DebugException_PrettyErrorResponse() {
		$expected = 'foobar';
		$exception = new Exception( $expected );
		$request = Mockery::mock( RequestInterface::class );
		$whoops = Mockery::mock( RunInterface::class );

		$request->shouldReceive( 'isAjax')
			->andReturn( false );

		$whoops->shouldReceive( RunInterface::EXCEPTION_HANDLER )
			->with( $exception )
			->andReturnUsing( function( $exception ) {
				echo $exception->getMessage();
			} );

		$subject = new ErrorHandler( $whoops, true );

		$this->assertEquals( $expected, $subject->getResponse( $request, $exception )->getBody()->read( strlen( $expected ) ) );
	}

	/**
	 * @covers ::getResponse
	 * @covers ::toResponse
	 * @expectedException \Exception
	 * @expectedExceptionMessage Rethrown exception
	 */
	public function testGetResponse_DebugException_RethrowException() {
		$exception = new Exception( 'Rethrown exception' );
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();
		$subject = new ErrorHandler( null, true );
		$subject->getResponse( $request, $exception );
	}
}
