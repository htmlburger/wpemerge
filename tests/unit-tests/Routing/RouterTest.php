<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Helpers\Handler;
use WPEmerge\Helpers\HandlerFactory;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
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
		$this->handler_factory = Mockery::mock( HandlerFactory::class )->shouldIgnoreMissing();
		$this->factory_handler = Mockery::mock( Handler::class );
		$this->subject = new Router( $this->condition_factory, $this->handler_factory );

		$this->handler_factory->shouldReceive( 'make' )
			->andReturn( $this->factory_handler );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->condition_factory );
		unset( $this->handler_factory );
		unset( $this->subject );
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
	 * @covers ::mergeMethodsAttribute
	 */
	public function testMergeMethodsAttribute() {
		$this->assertEquals( ['foo', 'bar'], $this->subject->mergeMethodsAttribute( ['foo'], ['bar'] ) );
	}

	/**
	 * @covers ::mergeConditionAttribute
	 */
	public function testMergeConditionAttribute_Valid_ConditionInterface() {
		$this->assertInstanceOf(
			ConditionInterface::class,
			$this->subject->mergeConditionAttribute( '', function () {} )
		);
	}

	/**
	 * @covers ::mergeConditionAttribute
	 */
	public function testMergeConditionAttribute_Empty_EmptyString() {
		$this->assertEquals(
			'',
			$this->subject->mergeConditionAttribute( '', '' )
		);
	}

	/**
	 * @covers ::mergeConditionAttribute
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage Route condition is not a valid
	 */
	public function testMergeConditionAttribute_Invalid_Exception() {
		$this->subject->mergeConditionAttribute( '', new \stdClass() );
	}

	/**
	 * @covers ::mergeMiddlewareAttribute
	 */
	public function testMergeMiddlewareAttribute() {
		$this->assertEquals( ['foo', 'bar'], $this->subject->mergeMiddlewareAttribute( ['foo'], ['bar'] ) );
	}

	/**
	 * @covers ::mergeNamespaceAttribute
	 */
	public function testMergeNamespaceAttribute() {
		$this->assertEquals( 'foo', $this->subject->mergeNamespaceAttribute( 'foo', '' ) );
		$this->assertEquals( 'bar', $this->subject->mergeNamespaceAttribute( 'foo', 'bar' ) );
	}

	/**
	 * @covers ::mergeHandlerAttribute
	 */
	public function testMergeHandlerAttribute() {
		$this->assertEquals( 'foo', $this->subject->mergeHandlerAttribute( 'foo', '' ) );
		$this->assertEquals( 'bar', $this->subject->mergeHandlerAttribute( 'foo', 'bar' ) );
	}

	/**
	 * @covers ::mergeQueryAttribute
	 */
	public function testMergeQueryAttribute() {
		$query1_called = false;
		$query2_called = false;

		$query1 = function ( $query_vars ) use ( &$query1_called ) {
			$query1_called = true;
			$this->assertEquals( ['foo' => 'foo'], $query_vars );
			return array_merge( $query_vars, ['bar'=>'bar'] );
		};

		$query2 = function ( $query_vars ) use ( &$query2_called ) {
			$query2_called = true;
			$this->assertEquals( ['foo' => 'foo', 'bar' => 'bar'], $query_vars );
			return array_merge( $query_vars, ['baz'=>'baz'] );
		};

		$this->assertNull( $this->subject->mergeQueryAttribute( null, null ) );
		$this->assertSame( $query1, $this->subject->mergeQueryAttribute( $query1, null ) );
		$this->assertSame( $query2, $this->subject->mergeQueryAttribute( null, $query2 ) );

		$combined = $this->subject->mergeQueryAttribute( $query1, $query2 );
		$combined( ['foo' => 'foo'] );

		$this->assertTrue( $query1_called );
		$this->assertTrue( $query2_called );
	}

	/**
	 * @covers ::mergeAttributes
	 */
	public function testMergeAttributes() {
		$this->assertEquals(
			[
				'methods' => [],
				'condition' => null,
				'middleware' => [],
				'namespace' => '',
				'handler' => '',
				'query' => null,
			],
			$this->subject->mergeAttributes( [], [] )
		);
	}

	/**
	 * @covers ::routeCondition
	 */
	public function testRouteCondition_Condition_ConditionInterface() {
		$subject = new RouterTestImplementation( $this->condition_factory, $this->handler_factory );

		$this->assertInstanceOf( ConditionInterface::class, $subject->publicRouteCondition( '/' ) );

		$this->assertInstanceOf( ConditionInterface::class, $subject->publicRouteCondition( function () {} ) );
	}

	/**
	 * @covers ::routeCondition
	 */
	public function testRouteCondition_ConditionInterface_Route() {
		$subject = new RouterTestImplementation( $this->condition_factory, $this->handler_factory );
		$condition = Mockery::mock( ConditionInterface::class );

		$this->assertSame( $condition, $subject->publicRouteCondition( $condition ) );
	}

	/**
	 * @covers ::routeCondition
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage No route condition specified
	 */
	public function testRouteCondition_NoCondition_Exception() {
		$subject = new RouterTestImplementation( $this->condition_factory, $this->handler_factory );

		$subject->publicRouteCondition( null );
	}

	/**
	 * @covers ::route
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage Route does not have any assigned request methods
	 */
	public function testRoute_NoMethods_Exception() {
		$this->subject->route( [] );
	}

	/**
	 * Quick and dirty test to cover basic group functionality.
	 * TODO Better and more extensive tests are needed.
	 *
	 * @covers ::route
	 */
	public function testRoute_Group_MergedAttributes() {
		$group_attributes = [
			'methods' => ['GET'],
			'condition' => ['url', 'foo/{foo}', ['foo' => '/^foo$/']],
			'middleware' => ['foo'],
			'namespace' => 'foo',
			'handler' => 'foo',
			'query' => function ( $query_vars ) {
				$query_vars['foo'] = 1;
				return $query_vars;
			}
		];

		$route_attributes = [
			'methods' => ['POST'],
			'condition' => ['url', 'bar/{bar}', ['bar' => '/^bar$/']],
			'middleware' => ['bar'],
			'namespace' => 'bar',
			'handler' => function () {},
			'query' => function ( $query_vars ) {
				$query_vars['bar'] = 1;
				return $query_vars;
			}
		];

		$route = null;

		$this->factory_handler->shouldReceive( 'get' )
			->andReturn( $route_attributes['handler'] );

		$this->subject->group( $group_attributes, function () use ( $route_attributes, &$route ) {
			$route = $this->subject->route( $route_attributes );
		} );

		$this->assertInstanceOf( RouteInterface::class, $route );
		$this->assertEquals( ['GET', 'POST'], $route->getMethods() );
		$this->assertEquals( '/foo/{foo}/bar/{bar}/', $route->getCondition()->getUrl() );
		$this->assertEquals( ['foo' => '/^foo$/', 'bar' => '/^bar$/'], $route->getCondition()->getUrlWhere() );
		$this->assertEquals( ['foo', 'bar'], $route->getMiddleware() );
		$this->assertSame( $route_attributes['handler'], $route->getHandler()->get() );
		$this->assertEquals( ['foo' => 1, 'bar' => 1], $route->getQueryFilter()( [] ) );

		// TODO namespace A: null; B: null
		// TODO namespace A: string; B: null
		// TODO namespace A: null; B: string
		// TODO namespace A: string; B: string
		// TODO query A: null; B: null
		// TODO query A: Closure; B: null
		// TODO query A: null; B: Closure
	}

	/**
	 * @covers ::execute
	 */
	public function testExecute_SatisfiedRoute_Route() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );

		$route->shouldReceive( 'getCondition' )
			->andReturn( $condition );

		$route->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$this->subject->addRoute( $route );

		$this->assertSame( $route, $this->subject->execute( $request, '' ) );
		$this->assertSame( $route, $this->subject->getCurrentRoute() );
	}

	/**
	 * @covers ::execute
	 */
	public function testExecute_UnsatisfiedRoutes_Null() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );
		$route1 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );
		$route2 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing( [] );

		$route1->shouldReceive( 'getCondition' )
			->andReturn( $condition );

		$route2->shouldReceive( 'getCondition' )
			->andReturn( $condition );

		$route1->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$route2->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$this->assertNull( $this->subject->execute( $request, '' ) );

		$this->subject->addRoute( $route1 );
		$this->subject->addRoute( $route2 );

		$this->assertNull( $this->subject->execute( $request, '' ) );
	}
}

class RouterTestImplementation extends Router {
	public function publicRouteCondition() {
		return call_user_func_array( [$this, 'routeCondition'], func_get_args() );
	}
}
