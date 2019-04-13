<?php

namespace WPEmergeTests\Routing;

use ArrayAccess;
use Closure;
use Exception;
use GuzzleHttp\Psr7;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Application\Application;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Kernels\HttpKernel;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\HasQueryFilterInterface;
use WPEmerge\Routing\RouteInterface;
use WPEmerge\Routing\Router;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Kernels\HttpKernel
 */
class HttpKernelTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->app = Mockery::mock( Application::class )->shouldIgnoreMissing();
		$this->router = Mockery::mock( Router::class )->shouldIgnoreMissing();
		$this->error_handler = Mockery::mock( ErrorHandlerInterface::class )->shouldIgnoreMissing();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->app );
		unset( $this->router );
		unset( $this->error_handler );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_SatisfiedRequest_Response() {
		$request = Mockery::mock( RequestInterface::class );
		$route = Mockery::mock( RouteInterface::class );
		$response = Mockery::mock( ResponseInterface::class );
		$arguments = ['foo', 'bar'];
		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$route->shouldReceive( 'getMiddleware' )
			->andReturn( [] );

		$this->router->shouldReceive( 'execute' )
			->andReturn( $route );

		$route->shouldReceive( 'handle' )
			->with( $request, $arguments )
			->andReturn( $response );

		$this->assertSame( $response, $subject->handle( $request, $arguments ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_UnsatisfiedRequest_Null() {
		$request = Mockery::mock( RequestInterface::class );
		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'execute' )
			->andReturn( null );

		$this->assertNull( $subject->handle( $request, [] ) );
	}

	/**
	 * @covers ::run
	 */
	public function testRun_Middleware_ExecutedInOrder() {
		$request = Mockery::mock( RequestInterface::class );

		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );
		$subject->setMiddleware( [
			'middleware2' => HttpKernelTestMiddlewareStub2::class,
			'middleware3' => HttpKernelTestMiddlewareStub3::class,
		] );
		$subject->setMiddlewareGroups( [
			'global' => [HttpKernelTestMiddlewareStub1::class],
		] );
		$subject->setMiddlewarePriority( [
			HttpKernelTestMiddlewareStub1::class,
			HttpKernelTestMiddlewareStub2::class,
		] );

		$response = $subject->run( $request, [
			'middleware3',
			'middleware2',
			'global',
		], function () {
			return ( new Psr7\Response() )->withBody( Psr7\stream_for( 'Handler' ) );
		} );

		$this->assertEquals( 'FooBarBazHandler', $response->getBody()->read( 999 ) );
	}

	/**
	 * @covers ::run
	 * @expectedException \Exception
	 * @expectedExceptionMessage Test exception handled
	 */
	public function testRun_Exception_UseErrorHandler() {
		$exception = new Exception();
		$request = Mockery::mock( RequestInterface::class );
		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$this->error_handler->shouldReceive( 'getResponse' )
			->with( $request, $exception )
			->andReturnUsing( function() {
				throw new Exception( 'Test exception handled' );
			} );

		$subject->run( $request, [], function () use ( $exception ) {
			throw $exception;
		} );
	}

	/**
	 * @covers ::filterRequest
	 */
	public function testFilterRequest_NoFilter_Unfiltered() {
		$route1 = Mockery::mock( RouteInterface::class );
		$route2 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$route3 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$route4 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$request = Mockery::mock( RequestInterface::class );
		$query_vars = ['unfiltered'];
		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'getRoutes' )
			->andReturn( [$route1, $route2, $route3] );

		$route1->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$route1->shouldNotReceive( 'applyQueryFilter' );

		$route2->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$route2->shouldNotReceive( 'applyQueryFilter' );

		$route3->shouldReceive( 'isSatisfied' )
			->andReturn( true )
			->once();

		$route3->shouldReceive( 'applyQueryFilter' )
			->with( $request, $query_vars )
			->andReturn( $query_vars );

		$route4->shouldNotReceive( 'isSatisfied' );

		$this->assertEquals( ['unfiltered'], $subject->filterRequest( $query_vars ) );
	}

	/**
	 * @covers ::filterRequest
	 */
	public function testFilterRequest_Filter_Filtered() {
		$route1 = Mockery::mock( RouteInterface::class );
		$route2 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$route3 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$request = Mockery::mock( RequestInterface::class );
		$query_vars = ['unfiltered'];
		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'getRoutes' )
			->andReturn( [$route1, $route2, $route3] );

		$route1->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$route1->shouldNotReceive( 'applyQueryFilter' );

		$route2->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route2->shouldReceive( 'applyQueryFilter' )
			->with( $request, $query_vars )
			->andReturn( ['filtered'] );

		$route3->shouldNotReceive( 'isSatisfied' );

		$this->assertEquals( ['filtered'], $subject->filterRequest( $query_vars ) );
	}

	/**
	 * @covers ::filterTemplateInclude
	 */
	public function testFilterTemplateInclude_Response_Override() {
		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();
		$container = Mockery::mock( ArrayAccess::class );
		$subject = Mockery::mock( HttpKernel::class, [$this->app, $request, $this->router, $this->error_handler] )->makePartial();

		$subject->shouldReceive( 'handle' )
			->andReturn( $response );

		$this->app->shouldReceive( 'getContainer' )
			->andReturn( $container );

		$container->shouldReceive( 'offsetSet' )
			->with( WPEMERGE_RESPONSE_KEY, $response );

		$this->assertEquals( WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php', $subject->filterTemplateInclude( '' ) );
	}

	/**
	 * @covers ::filterTemplateInclude
	 */
	public function testFilterTemplateInclude_404_ForcesWPQuery404() {
		global $wp_query;

		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class );
		$container = Mockery::mock( ArrayAccess::class );
		$subject = Mockery::mock( HttpKernel::class, [$this->app, $request, $this->router, $this->error_handler] )->makePartial();

		$response->shouldReceive( 'getStatusCode' )
			->andReturn( 404 );

		$subject->shouldReceive( 'handle' )
			->andReturn( $response );

		$this->app->shouldReceive( 'getContainer' )
			->andReturn( $container );

		$container->shouldReceive( 'offsetSet' )
			->with( WPEMERGE_RESPONSE_KEY, $response );

		$this->assertFalse( $wp_query->is_404() );
		$subject->filterTemplateInclude( '' );
		$this->assertTrue( $wp_query->is_404() );
	}

	/**
	 * @covers ::filterTemplateInclude
	 */
	public function testFilterTemplateInclude_NoResponse_Passthrough() {
		$request = Mockery::mock( RequestInterface::class );
		$subject = Mockery::mock( HttpKernel::class, [$this->app, $request, $this->router, $this->error_handler] )->makePartial();

		$subject->shouldReceive( 'handle' )
			->andReturn( null );

		$this->assertEquals( 'foo', $subject->filterTemplateInclude( 'foo' ) );
	}
}

class HttpKernelTestMiddlewareStub1 implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for(  'Foo' . $response->getBody()->read( 999 ) ) );
	}
}

class HttpKernelTestMiddlewareStub2 implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for(  'Bar' . $response->getBody()->read( 999 ) ) );
	}
}

class HttpKernelTestMiddlewareStub3 implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for(  'Baz' . $response->getBody()->read( 999 ) ) );
	}
}
