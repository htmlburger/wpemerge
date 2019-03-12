<?php

namespace WPEmergeTests\Routing;

use ArrayAccess;
use Exception;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Facades\Framework;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Router;
use WPEmerge\Routing\RouteInterface;
use WPEmerge\Support\Facade;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Router
 */
class RouterTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->error_handler = Mockery::mock( ErrorHandlerInterface::class )->shouldIgnoreMissing();
		$this->subject = new Router( Mockery::mock( RequestInterface::class ), [], [], 0, $this->error_handler );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		Facade::clearResolvedInstance( WPEMERGE_RESPONSE_KEY );
		Facade::clearResolvedInstance( WPEMERGE_FRAMEWORK_KEY );

		unset( $this->error_handler );
		unset( $this->subject );
	}

	/**
	 * @covers ::getMiddlewarePriority
	 */
	public function testGetMiddlewarePriority() {
		$default_middleware_priority = 100;
		$middleware1 = 'foo';
		$middleware1_priority = 99;
		$middleware2 = 'bar';
		$middleware3 = function() {};

		$subject = new Router( Mockery::mock( RequestInterface::class ), [], [
			$middleware1 => $middleware1_priority,
		], $default_middleware_priority, $this->error_handler );

		$this->assertEquals( $middleware1_priority, $subject->getMiddlewarePriority( $middleware1 ) );
		$this->assertEquals( $default_middleware_priority, $subject->getMiddlewarePriority( $middleware2 ) );
		$this->assertEquals( $default_middleware_priority, $subject->getMiddlewarePriority( $middleware3 ) );
	}

	/**
	 * @covers ::sortMiddleware
	 */
	public function testSortMiddleware() {
		$default_middleware_priority = 100;
		$middleware1 = 'foo';
		$middleware2 = 'bar';
		$middleware2_priority = 101;
		$middleware3 = 'baz';

		$subject = new Router( Mockery::mock( RequestInterface::class ), [], [
			$middleware2 => $middleware2_priority,
		], $default_middleware_priority, $this->error_handler );

		$result = $subject->sortMiddleware( [$middleware1, $middleware3, $middleware2] );

		$this->assertEquals( $middleware1, $result[0] );
		$this->assertEquals( $middleware3, $result[1] );
		$this->assertEquals( $middleware2, $result[2] );
	}

	/**
	 * @covers ::addRoute
	 */
	public function testAddRoute() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$subject = new Router( Mockery::mock( RequestInterface::class ), [], [], 0, $this->error_handler );

		$this->assertSame( $route, $subject->addRoute( $route ) );
	}

	/**
	 * @covers ::addRoute
	 */
	public function testAddRoute_GlobalMiddleware_PrependToRoutes() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
		$route_middleware = Mockery::mock( MiddlewareInterface::class );
		$global_middleware = Mockery::mock( MiddlewareInterface::class );
		$expected = [$global_middleware, $route_middleware];

		$subject = new Router( Mockery::mock( RequestInterface::class ), [$global_middleware], [], 0, $this->error_handler );

		$route->shouldReceive( 'getMiddleware' )
			  ->andReturn( [$route_middleware] )
			  ->once();

		$success = false;

		$route->shouldReceive( 'setMiddleware' )
			->andReturnUsing( function ( $middleware ) use ( $expected, &$success ) {
				// Do all of this because Mockery does not support passing a custom closure in with() to validate received arguments
				// and we need to do that because Mockery matches an array even if the values are out of order.
				$success = $middleware[0] === $expected[0];
			} )
			->once();

		$subject->addRoute( $route );

		$this->assertTrue( $success );
	}

	/**
	 * @covers ::getCurrentRoute
	 * @covers ::setCurrentRoute
	 */
	public function testSetCurrentRoute() {
		$expected = Mockery::mock( RouteInterface::class );

		$this->subject->setCurrentRoute( $expected );
		$this->assertSame( $expected, $this->subject->getCurrentRoute() );
	}

	/**
	 * @covers ::execute
	 */
	public function testExecute_Routes_CheckIfRoutesAreSatisfied() {
		$route1 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$route2 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );

		$route1->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$route2->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$this->subject->addRoute( $route1 );
		$this->subject->addRoute( $route2 );

		$this->subject->execute( '' );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::execute
	 */
	public function testExecute_SatisfiedRoute_StopCheckingCallHandleSetCurrent() {
		$route1 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$route2 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();

		$route1->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route1->shouldReceive( 'handle' )
			->andReturn( $response );

		$route2->shouldReceive( 'isSatisfied' )
			->never();

		$this->subject->addRoute( $route1 );
		$this->subject->addRoute( $route2 );

		$this->subject->execute( '' );

		$this->assertSame( $route1, $this->subject->getCurrentRoute() );
	}

	/**
	 * @covers ::execute
	 * @covers ::handle
	 */
	public function testExecute_Response() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();
		$container = Mockery::mock( ArrayAccess::class );

		$route->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route->shouldReceive( 'handle' )
			->andReturn( $response );

		$this->subject->addRoute( $route );

		Framework::shouldReceive( 'debugging' )
			->andReturn( false );

		Framework::shouldReceive( 'getContainer' )
			->andReturn( $container );

		$container->shouldReceive( 'offsetSet' )
			->with( WPEMERGE_RESPONSE_KEY, $response );

		$this->subject->execute( '' );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::execute
	 * @covers ::handle
	 */
	public function testExecute_Response_ReturnsBuiltInView() {
		$expected = WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php';
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();

		$route->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route->shouldReceive( 'handle' )
			->andReturn( $response );

		$this->subject->addRoute( $route );

		$this->assertSame( $expected, $this->subject->execute( '' ) );
	}

	/**
	 * @covers ::execute
	 * @covers ::handle
	 * @expectedException \Exception
	 * @expectedExceptionMessage Exception handled
	 */
	public function testExecute_Exception_UseErrorHandler() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$exception = new Exception();

		$route->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route->shouldReceive( 'handle' )
			->andReturnUsing( function() use ( $exception ) {
				throw $exception;
			} );

		$this->error_handler->shouldReceive( 'getResponse' )
			->with( $exception )
			->andReturnUsing( function() {
				throw new Exception( 'Exception handled' );
			} );

		$this->subject->addRoute( $route );

		$this->subject->execute( '' );
	}

	/**
	 * @covers ::handleAll
	 */
	public function testHandleAll() {
		$expected = $this->subject->any( '*' );

		$result = $this->subject->handleAll();

		$this->assertEquals( $expected, $result );
	}
}
