<?php

namespace WPEmergeTests\Middleware;

use Closure;
use GuzzleHttp\Psr7;
use Mockery;
use WPEmerge\Middleware\ExecutesMiddlewareTrait;
use WPEmerge\Requests\RequestInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Middleware\ExecutesMiddlewareTrait
 */
class ExecutesMiddlewareTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new ExecutesMiddlewareTraitTestImplementation();
		$this->request = Mockery::mock( RequestInterface::class );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
		unset( $this->request );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_EmptyList_CallsClosureOnce() {
		$response = $this->subject->executeMiddleware( [], $this->request, function () {
			return ( new Psr7\Response() )->withBody( Psr7\stream_for( 'Test complete' ) );
		} );

		$this->assertEquals( 'Test complete', $response->getBody()->read( 999 ) );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_ClassNames_CallsClassNamesFirstThenClosure() {
		$response = $this->subject->executeMiddleware(
			[
				[ExecutesMiddlewareTraitTestMiddlewareStub1::class],
				[ExecutesMiddlewareTraitTestMiddlewareStub2::class],
				[ExecutesMiddlewareTraitTestMiddlewareStub3::class],
			],
			$this->request,
			function () {
				return ( new Psr7\Response() )->withBody( Psr7\stream_for( 'Handler' ) );
			}
		);

		$this->assertEquals( 'Stub1Stub2Stub3Handler', $response->getBody()->read( 999 ) );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_ClassNameWithParameters_PassParameters() {
		$response = $this->subject->executeMiddleware(
			[
				[ExecutesMiddlewareTraitTestMiddlewareStubWithParameters::class, 'Foo', 'Bar'],
			],
			$this->request,
			function () {
				return new Psr7\Response();
			}
		);

		$this->assertEquals( 'FooBar', $response->getBody()->read( 999 ) );
	}
}

class ExecutesMiddlewareTraitTestImplementation {
	use ExecutesMiddlewareTrait;
}

class ExecutesMiddlewareTraitTestMiddlewareStub1 {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( 'Stub1' . $response->getBody()->read( 999 ) ) );
	}
}

class ExecutesMiddlewareTraitTestMiddlewareStub2 {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( 'Stub2' . $response->getBody()->read( 999 ) ) );
	}
}

class ExecutesMiddlewareTraitTestMiddlewareStub3 {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( 'Stub3' . $response->getBody()->read( 999 ) ) );
	}
}

class ExecutesMiddlewareTraitTestMiddlewareStubWithParameters {
	public function handle( RequestInterface $request, Closure $next, $param1, $param2 ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( $param1 . $param2 . $response->getBody()->read( 999 ) ) );
	}
}
