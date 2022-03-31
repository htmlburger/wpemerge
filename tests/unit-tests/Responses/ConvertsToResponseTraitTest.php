<?php

namespace WPEmergeTests\Middleware;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Responses\ConvertsToResponseTrait;
use WPEmerge\Responses\ResponsableInterface;
use WPEmerge\Responses\ResponseService;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Responses\ConvertsToResponseTrait
 */
class ConvertsToResponseTraitTest extends TestCase {
	public function set_up() {
		$this->response_service = Mockery::mock( ResponseService::class )->shouldIgnoreMissing();
		$this->subject = new ConvertsToResponseTraitImplementation( $this->response_service );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->response_service );
		unset( $this->subject );
	}

	/**
	 * @covers ::toResponse
	 */
	public function testToResponse_String_OutputResponse() {
		$expected = 'foobar';

		$this->response_service->shouldReceive( 'output' )
			->andReturnUsing( function ( $output ) {
				return (new Psr7Response())->withBody( Psr7\stream_for( $output ) );
			} );

		$response = $this->subject->publicToResponse( $expected );
		$this->assertEquals( $expected, $response->getBody()->read( strlen( $expected ) ) );
	}

	/**
	 * @covers ::toResponse
	 */
	public function testToResponse_Array_JsonResponse() {
		$input = ['foo' => 'bar'];
		$expected = json_encode( $input );

		$this->response_service->shouldReceive( 'json' )
			->andReturnUsing( function ( $data ) {
				return (new Psr7Response())->withBody( Psr7\stream_for( json_encode( $data ) ) );
			} );

		$response = $this->subject->publicToResponse( $input );
		$this->assertEquals( $expected, $response->getBody()->read( strlen( $expected ) ) );
	}

	/**
	 * @covers ::toResponse
	 */
	public function testToResponse_ResponsableInterface_Psr7Response() {
		$input = Mockery::mock( ResponsableInterface::class );
		$expected = ResponseInterface::class;

		$input->shouldReceive( 'toResponse' )
			->andReturn( Mockery::mock( ResponseInterface::class ) );

		$response = $this->subject->publicToResponse( $input );
		$this->assertInstanceOf( $expected, $response );
	}

	/**
	 * @covers ::toResponse
	 */
	public function testToResponse_Response_SameResponse() {
		$expected = Mockery::mock( ResponseInterface::class );

		$response = $this->subject->publicToResponse( $expected );
		$this->assertSame( $expected, $response );
	}
}

class ConvertsToResponseTraitImplementation {
	use ConvertsToResponseTrait;

	protected $response_service = null;

	public function __construct( $response_service ) {
		$this->response_service = $response_service;
	}

	protected function getResponseService() {
		return $this->response_service;
	}

	public function publicToResponse() {
		return call_user_func_array( [$this, 'toResponse'], func_get_args() );
	}
}
