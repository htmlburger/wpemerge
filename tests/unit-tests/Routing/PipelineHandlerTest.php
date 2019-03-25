<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Helpers\Handler;
use WPEmerge\Responses\ResponsableInterface;
use WPEmerge\Routing\PipelineHandler;
use Psr\Http\Message\ResponseInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\PipelineHandler
 */
class PipelineHandlerTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	/**
	 * @covers ::__construct
	 * @covers ::get
	 */
	public function testConstruct() {
		$closure = function() {};
		$expected = new Handler( $closure );

		$subject = new PipelineHandler( $closure );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::execute
	 * @covers ::getResponse()
	 */
	public function testExecute_ClosureReturningString_OutputResponse() {
		$expected = 'foobar';
		$closure = function( $value ) {
			return $value;
		};

		$subject = new PipelineHandler( $closure );
		$response = $subject->execute( $expected );
		$this->assertEquals( $expected, $response->getBody()->read( strlen( $expected ) ) );
	}

	/**
	 * @covers ::execute
	 * @covers ::getResponse()
	 */
	public function testExecute_ClosureReturningArray_JsonResponse() {
		$value = ['foo' => 'bar'];
		$expected = json_encode( $value );
		$closure = function( $value ) {
			return $value;
		};

		$subject = new PipelineHandler( $closure );
		$response = $subject->execute( $value );
		$this->assertEquals( $expected, $response->getBody()->read( strlen( $expected ) ) );
	}

	/**
	 * @covers ::execute
	 * @covers ::getResponse()
	 */
	public function testExecute_ResponsableInterface_Psr7Response() {
		$input = Mockery::mock( ResponsableInterface::class );
		$expected = ResponseInterface::class;
		$closure = function() use ( $input ) {
			return $input;
		};

		$input->shouldReceive( 'toResponse' )
			->andReturn( Mockery::mock( ResponseInterface::class ) );

		$subject = new PipelineHandler( $closure );
		$this->assertInstanceOf( $expected, $subject->execute() );
	}

	/**
	 * @covers ::execute
	 */
	public function testExecute_ClosureReturningResponse_SameResponse() {
		$expected = Mockery::mock( ResponseInterface::class );
		$closure = function() use ( $expected ) {
			return $expected;
		};

		$subject = new PipelineHandler( $closure );
		$this->assertSame( $expected, $subject->execute() );
	}

	/**
	 * @covers ::execute
	 * @covers ::getResponse()
	 * @expectedException \Exception
	 * @expectedExceptionMessage Response returned by controller is not valid
	 */
	public function testExecute_InvalidResponse_ThrowsException() {
		$closure = function() {
			return null;
		};

		$subject = new PipelineHandler( $closure );
		$subject->execute();
	}
}
