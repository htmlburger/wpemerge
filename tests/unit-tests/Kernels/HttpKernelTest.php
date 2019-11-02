<?php

namespace WPEmergeTests\Routing;

use Closure;
use Exception;
use GuzzleHttp\Psr7;
use Mockery;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Application\GenericFactory;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Helpers\Handler;
use WPEmerge\Helpers\HandlerFactory;
use WPEmerge\Kernels\HttpKernel;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Responses\ResponseService;
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

		$this->container = Mockery::mock( Container::class );
		$this->factory = Mockery::mock( GenericFactory::class )->shouldIgnoreMissing();
		$this->handler_factory = Mockery::mock( HandlerFactory::class )->shouldIgnoreMissing();
		$this->request = Mockery::mock( RequestInterface::class );
		$this->response_service = Mockery::mock( ResponseService::class )->shouldIgnoreMissing();
		$this->router = Mockery::mock( Router::class )->shouldIgnoreMissing();
		$this->error_handler = Mockery::mock( ErrorHandlerInterface::class )->shouldIgnoreMissing();
		$this->factory_handler = Mockery::mock( Handler::class );

		$this->handler_factory->shouldReceive( 'make' )
			->andReturn( $this->factory_handler );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->container );
		unset( $this->factory );
		unset( $this->handler_factory );
		unset( $this->request );
		unset( $this->response_service );
		unset( $this->router );
		unset( $this->error_handler );
		unset( $this->factory_handler );
	}

	/**
	 * @covers ::executeHandler
	 */
	public function testExecuteHandler_ValidResponse_Response() {
		$expected = Mockery::mock( ResponseInterface::class );
		$handler = function() use ( $expected ) {
			return $expected;
		};
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

		$this->factory_handler->shouldReceive( 'make' )
			->andReturn( $handler );

		$this->factory_handler->shouldReceive( 'execute' )
			->andReturnUsing( $handler );

		$this->assertSame( $expected, $subject->run( $this->request, [], $handler ) );
	}

	/**
	 * @covers ::executeHandler
	 * @expectedException \Exception
	 * @expectedExceptionMessage Response returned by controller is not valid
	 */
	public function testExecuteHandler_InvalidResponse_Exception() {
		$handler = function() {
			return null;
		};
		$error_handler = Mockery::mock( ErrorHandlerInterface::class )->shouldIgnoreMissing();
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $error_handler );

		$this->factory_handler->shouldReceive( 'make' )
			->andReturn( $handler );

		$this->factory_handler->shouldReceive( 'execute' )
			->andReturnUsing( $handler );

		$error_handler->shouldReceive( 'getResponse' )
			->andReturnUsing( function ( $request, $exception ) {
				throw $exception;
			} );

		$subject->run( $this->request, [], $handler );
	}

	/**
	 * @covers ::run
	 */
	public function testRun_Middleware_ExecutedInOrder() {
		$handler = function () {
			return ( new Psr7\Response() )->withBody( Psr7\stream_for( 'Handler' ) );
		};
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

		$this->factory_handler->shouldReceive( 'make' )
			->andReturn( $handler );

		$this->factory->shouldReceive( 'make' )
			->andReturnUsing( function ( $class ) {
				return new $class();
			} );

		$this->factory_handler->shouldReceive( 'execute' )
			->andReturnUsing( $handler );

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

		$response = $subject->run( $this->request, [
			'middleware3',
			'middleware2',
			'global',
		], $handler );

		$this->assertEquals( 'FooBarBazHandler', $response->getBody()->read( 999 ) );
	}

	/**
	 * @covers ::run
	 * @expectedException \Exception
	 * @expectedExceptionMessage Test exception handled
	 */
	public function testRun_Exception_UseErrorHandler() {
		$exception = new Exception();
		$handler = function () use ( $exception ) {
			throw $exception;
		};
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

		$this->factory_handler->shouldReceive( 'make' )
			->andReturn( $handler );

		$this->factory_handler->shouldReceive( 'execute' )
			->andReturnUsing( $handler );

		$this->error_handler->shouldReceive( 'getResponse' )
			->with( $this->request, $exception )
			->andReturnUsing( function() {
				throw new Exception( 'Test exception handled' );
			} );

		$subject->run( $this->request, [], $handler );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_SatisfiedRequest_Response() {
		$request = Mockery::mock( RequestInterface::class );
		$route = Mockery::mock( RouteInterface::class );
		$response = Mockery::mock( ResponseInterface::class );
		$arguments = ['foo', 'bar'];
		$route_arguments = ['baz'];
		$subject = Mockery::mock( HttpKernel::class, [$this->container, $this->factory, $this->handler_factory, $this->response_service, $request, $this->router, $this->error_handler] )->makePartial();

		$this->container->shouldReceive( 'offsetSet' );

		$this->router->shouldReceive( 'execute' )
			->andReturn( $route );

		$request->shouldReceive( 'withAttribute' )
			->andReturn( $request );

		$route->shouldReceive( 'getArguments' )
			->andReturn( $route_arguments );

		$route->shouldReceive( 'getMiddleware' )
			->andReturn( [] );

		$route->shouldReceive( 'getHandler' )
			->andReturn( $this->factory_handler );

		$subject->shouldReceive( 'run' )
			->andReturnUsing( function ( $request, $middleware, $handler ) use ( $response ) {
				return $response;
			} );

		$this->assertSame( $response, $subject->handle( $request, $arguments ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_UnsatisfiedRequest_Null() {
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'execute' )
			->andReturn( null );

		$this->assertNull( $subject->handle( $this->request, [] ) );
	}

	/**
	 * @covers ::respond
	 */
	public function testRespond_Response_Respond() {
		$response = Mockery::mock( ResponseInterface::class );
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

		$this->container->shouldReceive( 'offsetExists' )
			->with( WPEMERGE_RESPONSE_KEY )
			->andReturn( true );

		$this->container->shouldReceive( 'offsetGet' )
			->with( WPEMERGE_RESPONSE_KEY )
			->andReturn( $response );

		$this->response_service->shouldReceive( 'respond' )
			->with( $response )
			->once();

		$subject->respond();

		$this->assertTrue( true );
	}

	/**
	 * @covers ::respond
	 */
	public function testRespond_NoResponse_DoNotRespond() {
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

		$this->container->shouldReceive( 'offsetExists' )
			->with( WPEMERGE_RESPONSE_KEY )
			->andReturn( false );

		$this->response_service->shouldNotReceive( 'respond' );

		$subject->respond();

		$this->assertTrue( true );
	}

	/**
	 * @covers ::filterRequest
	 */
	public function testFilterRequest_NoFilter_Unfiltered() {
		$route1 = Mockery::mock( RouteInterface::class );
		$route2 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$route3 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$route4 = Mockery::mock( RouteInterface::class, HasQueryFilterInterface::class );
		$query_vars = ['unfiltered'];
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

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
			->with( $this->request, $query_vars )
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
		$query_vars = ['unfiltered'];
		$subject = new HttpKernel( $this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'getRoutes' )
			->andReturn( [$route1, $route2, $route3] );

		$route1->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$route1->shouldNotReceive( 'applyQueryFilter' );

		$route2->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route2->shouldReceive( 'applyQueryFilter' )
			->with( $this->request, $query_vars )
			->andReturn( ['filtered'] );

		$route3->shouldNotReceive( 'isSatisfied' );

		$this->assertEquals( ['filtered'], $subject->filterRequest( $query_vars ) );
	}

	/**
	 * @covers ::filterTemplateInclude
	 */
	public function testFilterTemplateInclude_Response_Override() {
		$response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();
		$subject = Mockery::mock( HttpKernel::class, [$this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler] )->makePartial();

		$subject->shouldReceive( 'handle' )
			->andReturn( $response );

		$this->container->shouldReceive( 'offsetSet' )
			->with( WPEMERGE_RESPONSE_KEY, $response );

		$this->assertEquals( WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php', $subject->filterTemplateInclude( '' ) );
	}

	/**
	 * @covers ::filterTemplateInclude
	 */
	public function testFilterTemplateInclude_404_ForcesWPQuery404() {
		global $wp_query;

		$response = Mockery::mock( ResponseInterface::class );
		$subject = Mockery::mock( HttpKernel::class, [$this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler] )->makePartial();

		$response->shouldReceive( 'getStatusCode' )
			->andReturn( 404 );

		$subject->shouldReceive( 'handle' )
			->andReturn( $response );

		$this->container->shouldReceive( 'offsetSet' )
			->with( WPEMERGE_RESPONSE_KEY, $response );

		$this->assertFalse( $wp_query->is_404() );
		$subject->filterTemplateInclude( '' );
		$this->assertTrue( $wp_query->is_404() );
	}

	/**
	 * @covers ::filterTemplateInclude
	 */
	public function testFilterTemplateInclude_NoResponse_Passthrough() {
		$subject = Mockery::mock( HttpKernel::class, [$this->container, $this->factory, $this->handler_factory, $this->response_service, $this->request, $this->router, $this->error_handler] )->makePartial();

		$subject->shouldReceive( 'handle' )
			->andReturn( null );

		$this->assertEquals( 'foo', $subject->filterTemplateInclude( 'foo' ) );
	}
}

class HttpKernelTestMiddlewareStub1 {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( 'Foo' . $response->getBody()->read( 999 ) ) );
	}
}

class HttpKernelTestMiddlewareStub2 {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( 'Bar' . $response->getBody()->read( 999 ) ) );
	}
}

class HttpKernelTestMiddlewareStub3 {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( 'Baz' . $response->getBody()->read( 999 ) ) );
	}
}

class HttpKernelTestMiddlewareStubWithParameters {
	public function handle( RequestInterface $request, Closure $next, $param1, $param2 ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for( $param1 . $param2 . $response->getBody()->read( 999 ) ) );
	}
}
