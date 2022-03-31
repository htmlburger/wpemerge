<?php

namespace WPEmergeTests\Exceptions;

use Exception;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Whoops\RunInterface;
use WPEmerge\Exceptions\ErrorHandler;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Responses\ResponseService;
use WPEmerge\Routing\NotFoundException;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Exceptions\ErrorHandler
 */
class ErrorHandlerTest extends TestCase {
	public function set_up() {
		$this->response_service = Mockery::mock( ResponseService::class );
		$this->subject = new ErrorHandler( $this->response_service, null, false );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->response_service );
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

		$this->response_service->shouldReceive( 'error' )
			->andReturnUsing( function ( $status ) {
				return (new Response())->withStatus( $status );
			} );

		$this->assertEquals( $expected, $this->subject->getResponse( $request, $exception )->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 * @covers ::toResponse
	 */
	public function testGetResponse_ProductionException_500Response() {
		$expected = 500;
		$exception = new Exception();
		$request = Mockery::mock( RequestInterface::class );

		$this->response_service->shouldReceive( 'error' )
			->andReturnUsing( function ( $status ) {
				return (new Response())->withStatus( $status );
			} );


		$error_log = tmpfile();
		$old_error_log = ini_set( 'error_log', stream_get_meta_data( $error_log )['uri'] );
		$this->assertEquals( $expected, $this->subject->getResponse( $request, $exception )->getStatusCode() );
		ini_set( 'error_log', $old_error_log );
		$this->assertTrue( strpos( stream_get_contents( $error_log ), 'ErrorHandlerTest.php:59' ) !== -1 );
	}

	/**
	 * @covers ::getResponse
	 * @covers ::toDebugResponse
	 */
	public function testGetResponse_AjaxException_JsonResponse() {
		$exception = new Exception();
		$request = Mockery::mock( RequestInterface::class );
		$subject = new ErrorHandler( $this->response_service, null, true );

		$request->shouldReceive( 'isAjax' )
			->andReturn( true );

		$this->response_service->shouldReceive( 'json' )
			->andReturnUsing( function ( $data ) {
				return (new Response())
					->withHeader( 'Content-Type', 'application/json' )
					->withBody( Psr7\stream_for( wp_json_encode( $data ) ) );
			} );

		$response = $subject->getResponse( $request, $exception );
		$response_body = $response->getBody();

		$response_text = '';
		while ( ! $response_body->eof() ) {
			$response_text .= $response_body->read( 4096 );
		}
		$response_json = json_decode( $response_text, true );

		$this->assertArrayHasKey( 'exception', $response_json );
		$this->assertEquals( 500, $response->getStatusCode() );
	}

	/**
	 * @covers ::getResponse
	 * @covers ::toDebugResponse
	 */
	public function testGetResponse_DebugException_PrettyErrorResponse() {
		$expected = 'foobar';
		$exception = new Exception( $expected );
		$request = Mockery::mock( RequestInterface::class );
		$whoops = Mockery::mock( RunInterface::class );

		$request->shouldReceive( 'isAjax' )
			->andReturn( false );

		$whoops->shouldReceive( RunInterface::EXCEPTION_HANDLER )
			->with( $exception )
			->andReturnUsing( function( $exception ) {
				echo $exception->getMessage();
			} );

		$this->response_service->shouldReceive( 'output' )
			->andReturnUsing( function ( $output ) {
				return (new Response())
					->withBody( Psr7\stream_for( $output ) );
			} );

		$subject = new ErrorHandler( $this->response_service, $whoops, true );

		$this->assertEquals( $expected, $subject->getResponse( $request, $exception )->getBody()->read( strlen( $expected ) ) );
	}

	/**
	 * @covers ::getResponse
	 * @covers ::toDebugResponse
	 * @expectedException \Exception
	 * @expectedExceptionMessage Rethrown exception
	 */
	public function testGetResponse_DebugException_RethrowException() {
		$exception = new Exception( 'Rethrown exception' );
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();
		$subject = new ErrorHandler( $this->response_service, null, true );
		$subject->getResponse( $request, $exception );
	}
}
