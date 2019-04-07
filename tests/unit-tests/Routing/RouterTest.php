<?php

namespace WPEmergeTests\Routing;

use ArrayAccess;
use Exception;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Exceptions\ErrorHandlerInterface;
use WPEmerge\Facades\Application;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\MultipleCondition;
use WPEmerge\Routing\Conditions\UrlCondition;
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
		$this->condition_factory = new ConditionFactory( [
			'url' => \WPEmerge\Routing\Conditions\UrlCondition::class,
			'custom' => \WPEmerge\Routing\Conditions\CustomCondition::class,
			'multiple' => \WPEmerge\Routing\Conditions\MultipleCondition::class,
			'negate' => \WPEmerge\Routing\Conditions\NegateCondition::class,
		] );
		$this->subject = new Router( $this->condition_factory, $this->error_handler );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		Facade::clearResolvedInstance( WPEMERGE_RESPONSE_KEY );
		Facade::clearResolvedInstance( WPEMERGE_APPLICATION_KEY );

		unset( $this->error_handler );
		unset( $this->condition_factory );
		unset( $this->subject );
	}

	/**
	 * @covers ::getMiddlewarePriority
	 */
	public function testGetMiddlewarePriority() {
		$middleware1 = 'foo';
		$middleware2 = 'bar';
		$middleware3 = 'baz';
		$middleware4 = function() {};

		$subject = new Router( $this->condition_factory, $this->error_handler );
		$subject->setMiddlewarePriority( [
			$middleware1,
			$middleware2,
		] );

		$this->assertEquals( 1, $subject->getMiddlewarePriority( $middleware1 ) );
		$this->assertEquals( 0, $subject->getMiddlewarePriority( $middleware2 ) );
		$this->assertEquals( -1, $subject->getMiddlewarePriority( $middleware3 ) );
		$this->assertEquals( -1, $subject->getMiddlewarePriority( $middleware4 ) );
	}

	/**
	 * @covers ::sortMiddleware
	 */
	public function testSortMiddleware() {
		$middleware1 = 'foo';
		$middleware2 = 'bar';
		$middleware3 = 'baz';

		$subject = new Router( $this->condition_factory, $this->error_handler );
		$subject->setMiddlewarePriority( [
			$middleware2,
		] );

		$result = $subject->sortMiddleware( [$middleware1, $middleware3, $middleware2] );

		$this->assertEquals( $middleware2, $result[0] );
		$this->assertEquals( $middleware1, $result[1] );
		$this->assertEquals( $middleware3, $result[2] );
	}

	/**
	 * Quick and dirty test to cover basic group functionality.
	 * Better and more extensive tests are needed.
	 *
	 * @covers ::group
	 */
	public function testGroup() {
		$condition1 = Mockery::mock( ConditionInterface::class );
		$condition2 = Mockery::mock( UrlCondition::class );
		$middleware1 = function () {};
		$middleware2 = function () {};
		$middleware3 = function () {};
		$where1 = ['foo' => 'foo'];
		$where2 = ['bar' => 'bar'];

		$subject = new Router( $this->condition_factory, $this->error_handler );
		$subject->setGlobalMiddleware( [$middleware1] );

		$mock = Mockery::mock( RouteInterface::class );

		$mock->shouldReceive( 'getCondition' )
			->andReturn( $condition2 );

		$condition2->shouldReceive( 'getUrlWhere' )
			->andReturn( $where2 );

		$condition2->shouldReceive( 'setUrlWhere' )
			->with( array_merge( $where1, $where2 ) );

		$mock->shouldReceive( 'setCondition' )
			->with( Mockery::on( function ( $condition ) use ( $condition1, $condition2 ) {
				$is_multiple = $condition instanceof MultipleCondition;
				$conditions = $condition->getConditions();
				$condition1_matches = $conditions[0] === $condition1;
				$condition2_matches = $conditions[1] === $condition2;

				return $is_multiple && $condition1_matches && $condition2_matches;
			} ) );

		$mock->shouldReceive( 'getMiddleware' )
			->andReturn( [$middleware3] );

		$mock->shouldReceive( 'setMiddleware' )
			->with( [$middleware1, $middleware2, $middleware3] );

		$subject->group( [
			'condition' => $condition1,
			'where' => $where1,
			'middleware' => $middleware2,
		], function () use ( $subject, $mock ) {
			$subject->addRoute( $mock );
		} );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::addRoute
	 */
	public function testAddRoute() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$subject = new Router( $this->condition_factory, $this->error_handler );

		$this->assertSame( $route, $subject->addRoute( $route ) );
	}

	/**
	 * @covers ::addRoute
	 */
	public function testAddRoute_GlobalMiddleware_PrependToRoute() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
		$route_middleware = Mockery::mock( MiddlewareInterface::class );
		$global_middleware = Mockery::mock( MiddlewareInterface::class );
		$expected = [$global_middleware, $route_middleware];

		$subject = new Router( $this->condition_factory, $this->error_handler );
		$subject->setGlobalMiddleware( [$global_middleware] );

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
	 * @covers ::handleAll
	 */
	public function testHandleAll() {
		$expected = $this->subject->any( '*' );

		$result = $this->subject->handleAll();

		$this->assertEquals( $expected, $result );
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
	public function testExecute_UnsatisfiedRoutes_Null() {
		$request = Mockery::mock( RequestInterface::class );
		$route1 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$route2 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );

		$route1->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$route2->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$this->subject->addRoute( $route1 );
		$this->subject->addRoute( $route2 );

		$this->assertEquals( null, $this->subject->execute( $request, '' ) );
	}

	/**
	 * @covers ::execute
	 * @covers ::handle
	 */
	public function testExecute_SatisfiedRoute_Response() {
		$request = Mockery::mock( RequestInterface::class );
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();

		$route->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route->shouldReceive( 'handle' )
			->andReturn( $response );

		$this->subject->addRoute( $route );

		$this->assertSame( $response, $this->subject->execute( $request, '' ) );
	}

	/**
	 * @covers ::execute
	 * @covers ::handle
	 * @expectedException \Exception
	 * @expectedExceptionMessage Test exception handled
	 */
	public function testExecute_Exception_UseErrorHandler() {
		$request = Mockery::mock( RequestInterface::class );
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
				throw new Exception( 'Test exception handled' );
			} );

		$this->subject->addRoute( $route );

		$this->subject->execute( $request, '' );
	}
}
