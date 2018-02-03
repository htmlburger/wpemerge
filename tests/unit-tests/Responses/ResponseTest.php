<?php

namespace WPEmergeTests\Responses;

use Mockery;
use WPEmerge\Requests\Request;
use WPEmerge\Responses\RedirectResponse;
use WPEmerge\Responses\Response;
use Psr\Http\Message\ResponseInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Responses\Response
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
		$this->assertInstanceOf( ResponseInterface::class, Response::response() );
	}

	/**
	 * @covers ::output
	 */
	public function testOutut() {
		$expected = 'foobar';

		$subject = Response::output( $expected );
		$this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
	}

	/**
	 * @covers ::json
	 */
	public function testJson() {
		$input = array( 'foo' => 'bar' );
		$expected = json_encode( $input );

		$subject = Response::json( $input );
		$this->assertEquals( $expected, $this->readStream( $subject->getBody() ) );
	}

	/**
	 * @covers ::redirect
	 */
	public function testRedirect() {
		$this->assertInstanceOf( RedirectResponse::class, Response::redirect() );
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
