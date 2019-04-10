<?php

namespace WPEmergeTests\Routing;

use ArrayAccess;
use Exception;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Application\Application;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Kernels\HttpKernel;
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
	public function testHandle_ValidRequest_Response() {
		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class );
		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'execute' )
			->andReturn($response);

		$this->assertSame( $response, $subject->handle( $request, '' ) );
	}

	/**
	 * @covers ::handle
	 * @expectedException \Exception
	 * @expectedExceptionMessage Test exception handled
	 */
	public function testHandle_Exception_UseErrorHandler() {
		$exception = new Exception();
		$request = Mockery::mock( RequestInterface::class );
		$subject = new HttpKernel( $this->app, $request, $this->router, $this->error_handler );

		$this->router->shouldReceive( 'execute' )
			->andReturnUsing( function() use ( $exception ) {
				throw $exception;
			} );

		$this->error_handler->shouldReceive( 'getResponse' )
			->with( $request, $exception )
			->andReturnUsing( function() {
				throw new Exception( 'Test exception handled' );
			} );

		$subject->handle( $request, '' );
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
	public function testFilterTemplateInclude_NoResponse_Passthrough() {
		$request = Mockery::mock( RequestInterface::class );
		$subject = Mockery::mock( HttpKernel::class, [$this->app, $request, $this->router, $this->error_handler] )->makePartial();

		$subject->shouldReceive( 'handle' )
			->andReturn( null );

		$this->assertEquals( 'foo', $subject->filterTemplateInclude( 'foo' ) );
	}
}
