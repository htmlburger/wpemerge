<?php

namespace WPEmergeTests\Middleware;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Mockery;
use WP_UnitTestCase;
use WPEmerge\Application\InjectionFactory;
use WPEmerge\Middleware\ExecutesMiddlewareTrait;
use WPEmerge\Requests\RequestInterface;
use WPEmergeTests\Routing\HttpKernelTestMiddlewareStub1;
use WPEmergeTests\Routing\HttpKernelTestMiddlewareStub2;
use WPEmergeTests\Routing\HttpKernelTestMiddlewareStub3;
use WPEmergeTests\Routing\HttpKernelTestMiddlewareStubWithParameters;

/**
 * @coversDefaultClass \WPEmerge\Middleware\ExecutesMiddlewareTrait
 */
class ExecutesMiddlewareTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->request = Mockery::mock( RequestInterface::class );
		$this->injection_factory = Mockery::mock( InjectionFactory::class );
		$this->subject = new ExecutesMiddlewareTraitImplementation( $this->injection_factory );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->request );
		unset( $this->injection_factory );
		unset( $this->subject );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_EmptyList_CallsClosure() {
		$response = $this->subject->publicExecuteMiddleware(
			[],
			$this->request,
			function () {
				return (new Psr7Response())->withBody( Psr7\stream_for( 'Handler' ) );
			}
		);

		$this->assertEquals( 'Handler', $response->getBody()->read( 999 ) );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_ClassNames_CallsClassNamesFirstThenClosure() {
		$this->injection_factory->shouldReceive( 'make' )
			->andReturnUsing( function ( $class ) {
				return new $class();
			} );

		$response = $this->subject->publicExecuteMiddleware(
			[
				[HttpKernelTestMiddlewareStub1::class],
				[HttpKernelTestMiddlewareStub2::class],
				[HttpKernelTestMiddlewareStub3::class],
			],
			$this->request,
			function () {
				return (new Psr7Response())->withBody( Psr7\stream_for( 'Handler' ) );
			}
		);

		$this->assertEquals( 'FooBarBazHandler', $response->getBody()->read( 999 ) );
	}

	/**
	 * @covers ::executeMiddleware
	 */
	public function testExecuteMiddleware_ClassNameWithParameters_PassParameters() {
		$this->injection_factory->shouldReceive( 'make' )
			->andReturnUsing( function ( $class ) {
				return new $class();
			} );

		$response = $this->subject->publicExecuteMiddleware(
			[
				[HttpKernelTestMiddlewareStubWithParameters::class, 'Arg1', 'Arg2'],
			],
			$this->request,
			function () {
				return (new Psr7Response())->withBody( Psr7\stream_for( 'Handler' ) );
			}
		);

		$this->assertEquals( 'Arg1Arg2Handler', $response->getBody()->read( 999 ) );
	}
}

class ExecutesMiddlewareTraitImplementation {
	use ExecutesMiddlewareTrait;

	protected $injection_factory = null;

	public function __construct( $injection_factory ) {
		$this->injection_factory = $injection_factory;
	}

	protected function makeMiddleware( $class ) {
		return $this->injection_factory->make( $class );
	}

	public function publicExecuteMiddleware() {
		return call_user_func_array( [$this, 'executeMiddleware'], func_get_args() );
	}
}
