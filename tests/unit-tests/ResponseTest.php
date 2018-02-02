<?php

namespace WPEmergeTests;

use Mockery;
use WPEmerge\Request;
use WPEmerge\Response;
use Psr\Http\Message\ResponseInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Response
 */
class ResponseTest extends WP_UnitTestCase {
	public function tearDown() {
		parent::tearDown();

		Mockery::close();
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
		$expected = ResponseInterface::class;

		$subject = Response::response();
		$this->assertInstanceOf( $expected, $subject );
	}

	/**
	 * @covers ::output
	 */
	public function testOutut() {
		$expected = 'foobar';

		$subject = Response::output( Response::response(), $expected );
		$this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
	}

	/**
	 * @covers ::view
	 */
	public function testView() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';
		$expected = file_get_contents( $view );

		// Relies on PhpView - it should be mocked instead
		$subject = Response::view( $view )->toResponse();
		$this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
	}

	/**
	 * @covers ::json
	 */
	public function testJson() {
		$input = array( 'foo' => 'bar' );
		$expected = json_encode( $input );

		$subject = Response::json( Response::response(), $input );
		$this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
	}

	/**
	 * @covers ::redirect
	 */
	public function testRedirect_Location() {
		$expected = '/foobar';

		$subject = Response::redirect( Response::response(), $expected );
		$this->assertEquals( $expected, $subject->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::redirect
	 */
	public function testRedirect_Status() {
		$expected1 = 301;
		$expected2 = 302;

		$subject1 = Response::redirect( Response::response(), 'foobar', $expected1 );
		$this->assertEquals( $expected1, $subject1->getStatusCode() );

		$subject2 = Response::redirect( Response::response(), 'foobar', $expected2 );
		$this->assertEquals( $expected2, $subject2->getStatusCode() );
	}

	/**
	 * @covers ::reload
	 */
	public function testReload_Location() {
		$expected = 'http://example.com/foobar?hello=world';
		$request_mock = Mockery::mock( Request::class );

		$request_mock->shouldReceive( 'getUrl' )
			->once()
			->andReturn( $expected );

		$subject = Response::reload( Response::response(), $request_mock );
		$this->assertEquals( $expected, $subject->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::reload
	 */
	public function testReload_Status() {
		$expected1 = 301;
		$expected2 = 302;
		$url = 'http://example.com/foobar?hello=world';
		$request_mock = Mockery::mock( Request::class );

		$request_mock->shouldReceive( 'getUrl' )
			->andReturn( $url );

		$subject1 = Response::reload( Response::response(), $request_mock, $expected1 );
		$this->assertEquals( $expected1, $subject1->getStatusCode() );

		$subject2 = Response::reload( Response::response(), $request_mock, $expected2 );
		$this->assertEquals( $expected2, $subject2->getStatusCode() );
	}

	/**
	 * @covers ::error
	 */
	public function testError() {
		$expected1 = 404;
		$expected2 = 500;

		$subject1 = Response::error( $expected1 );
		$this->assertEquals( $expected1, $subject1->getStatusCode() );

		$subject2 = Response::error( $expected2 );
		$this->assertEquals( $expected2, $subject2->getStatusCode() );
	}
}
