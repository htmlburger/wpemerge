<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Pipeline;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Pipeline
 */
class PipelineTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	/**
	 * @covers ::run
	 */
	public function testRun() {
		$request = Mockery::mock( RequestInterface::class );
		$view = 'foobar.php';
		$expected = 'foo';
		$handler = function( $r, $v ) use ( $request, $view, $expected ) {
			if ( $r !== $request ) {
				return 'Mismatched $request';
			}

			if ( $v !== $view ) {
				return 'Mismatched $view';
			}

			return $expected;
		};
		$subject = ( new Pipeline() )->to( $handler );

		$this->assertSame( $expected, $subject->run( $request, [$request, $view] )->getBody()->read( 100 ) );
	}
}
