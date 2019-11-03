<?php

namespace WPEmergeTests\Responses;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Responses\RedirectResponse;
use WPEmerge\Responses\ResponseService;
use WP_UnitTestCase;
use WPEmerge\View\ViewInterface;
use WPEmerge\View\ViewService;

/**
 * @coversDefaultClass \WPEmerge\Responses\ResponseService
 */
class ResponseServiceTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->request = Mockery::mock( RequestInterface::class );
		$this->view_service = Mockery::mock( ViewService::class );
		$this->subject = new ResponseService( $this->request, $this->view_service );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->request );
		unset( $this->view_service );
		unset( $this->subject );
	}

	protected function readStream( $stream, $chunk_size = 4096 ) {
		$output = '';
		while ( ! $stream->eof() ) {
			$output .= $stream->read( $chunk_size );
		}
		return $output;
	}

	/**
	 * @covers ::response
	 */
	public function testResponse() {
		$this->assertInstanceOf( ResponseInterface::class, $this->subject->response() );
	}

	/**
	 * @covers ::output
	 */
	public function testOutut() {
		$expected = 'foobar';

		$subject = $this->subject->output( $expected );
		$this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
	}

	/**
	 * @covers ::json
	 */
	public function testJson() {
		$input = ['foo' => 'bar'];
		$expected = json_encode( $input );

		$subject = $this->subject->json( $input );
		$this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
	}

	/**
	 * @covers ::redirect
	 */
	public function testRedirect() {
		$this->request->shouldReceive( 'getHeaderLine' )
			->with( 'Referer' )
			->andReturn( 'foo' );

		$this->assertEquals( 'foo', $this->subject->redirect()->back()->getHeaderLine( 'Location' ) );

		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getHeaderLine' )
			->with( 'Referer' )
			->andReturn( 'bar' );

		$this->assertEquals( 'bar', $this->subject->redirect( $request )->back()->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::error
	 */
	public function testError() {
		$expected1 = 404;
		$expected2 = 500;

		$view = Mockery::mock( ViewInterface::class );
		$response = Mockery::mock( ResponseInterface::class );

		$this->view_service->shouldReceive( 'make' )
			->andReturn( $view );

		$view->shouldReceive( 'toResponse' )
			->andReturn( $response );

		$response->shouldReceive( 'withStatus' )
			->with( $expected1 )
			->andReturn( $response )
			->once()
			->ordered();

		$response->shouldReceive( 'withStatus' )
			->with( $expected2 )
			->andReturn( $response )
			->once()
			->ordered();

		$response1 = $this->subject->error( $expected1 );
		$response2 = $this->subject->error( $expected2 );

		$this->assertSame( $response, $response1 );
		$this->assertSame( $response, $response2 );
	}
}
