<?php

namespace WPEmergeTests\Routing;

use Closure;
use GuzzleHttp\Psr7;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\Request;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\MultipleCondition;
use WPEmerge\Routing\Conditions\UrlCondition;
use WPEmerge\Routing\Router;
use WPEmerge\Routing\RouteInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Router
 */
class RouterTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->condition_factory = new ConditionFactory( [
			'url' => \WPEmerge\Routing\Conditions\UrlCondition::class,
			'custom' => \WPEmerge\Routing\Conditions\CustomCondition::class,
			'multiple' => \WPEmerge\Routing\Conditions\MultipleCondition::class,
			'negate' => \WPEmerge\Routing\Conditions\NegateCondition::class,
		] );
		$this->subject = new Router( $this->condition_factory );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

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

		$subject = new Router( $this->condition_factory );
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

		$subject = new Router( $this->condition_factory );
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
		$where1 = ['foo' => 'foo'];
		$where2 = ['bar' => 'bar'];

		$subject = new Router( $this->condition_factory );

		$subject->setMiddleware( [
			'middleware1' => RouterTestMiddlewareStub1::class,
			'middleware2' => RouterTestMiddlewareStub2::class,
		] );

		$route = Mockery::mock( RouteInterface::class );

		$route->shouldReceive( 'getCondition' )
			->andReturn( $condition2 );

		$condition2->shouldReceive( 'getUrlWhere' )
			->andReturn( $where2 );

		$condition2->shouldReceive( 'setUrlWhere' )
			->with( array_merge( $where1, $where2 ) );

		$route->shouldReceive( 'setCondition' )
			->with( Mockery::on( function ( $condition ) use ( $condition1, $condition2 ) {
				$is_multiple = $condition instanceof MultipleCondition;
				$conditions = $condition->getConditions();
				$condition1_matches = $conditions[0] === $condition1;
				$condition2_matches = $conditions[1] === $condition2;

				return $is_multiple && $condition1_matches && $condition2_matches;
			} ) );

		$route->shouldReceive( 'getMiddleware' )
			->andReturn( ['middleware2'] );

		$route->shouldReceive( 'setMiddleware' )
			->with( ['middleware1', 'middleware2'] );

		$subject->group( [
			'condition' => $condition1,
			'where' => $where1,
			'middleware' => 'middleware1',
		], function () use ( $subject, $route ) {
			$subject->addRoute( $route );
		} );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::addRoute
	 */
	public function testAddRoute() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$subject = new Router( $this->condition_factory );

		$this->assertSame( $route, $subject->addRoute( $route ) );
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
	 * @covers ::handle
	 */
	public function testHandle_Middleware_ExecutedInOrder() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();

		$subject = new Router( $this->condition_factory );
		$subject->setMiddleware( [
			'middleware2' => RouterTestMiddlewareStub2::class,
			'middleware3' => RouterTestMiddlewareStub3::class,
		] );
		$subject->setGlobalMiddleware( [
			RouterTestMiddlewareStub1::class,
		] );
		$subject->setMiddlewarePriority( [
			RouterTestMiddlewareStub2::class,
			RouterTestMiddlewareStub3::class,
		] );

		$route->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$route->shouldReceive( 'getMiddleware' )
			->andReturn( [
				'middleware3',
				'middleware2',
			] );

		$route->shouldReceive( 'handle' )
			->andReturnUsing( function ( $middleware ) {
				return (new Psr7\Response())->withBody( Psr7\stream_for( 'Handler' ) );
			} );

		$subject->addRoute( $route );

		$response = $subject->execute( Mockery::mock( RequestInterface::class ), '' );

		$this->assertEquals( 'FooBarBazHandler', $response->getBody()->read( 999 ) );
	}
}

class RouterTestMiddlewareStub1 implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for(  'Foo' . $response->getBody()->read( 999 ) ) );
	}
}

class RouterTestMiddlewareStub2 implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for(  'Bar' . $response->getBody()->read( 999 ) ) );
	}
}

class RouterTestMiddlewareStub3 implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		return $response->withBody( Psr7\stream_for(  'Baz' . $response->getBody()->read( 999 ) ) );
	}
}
