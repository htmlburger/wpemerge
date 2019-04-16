<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Helpers\Handler;
use WPEmerge\Routing\Conditions\ConditionFactory;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\UrlCondition;
use WPEmerge\Routing\HasRoutesTrait;
use WPEmerge\Routing\Route;
use WPEmerge\Routing\RouteInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\HasRoutesTrait
 */
class HasRoutesTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->condition_factory = new ConditionFactory( [
			'url' => \WPEmerge\Routing\Conditions\UrlCondition::class,
			'custom' => \WPEmerge\Routing\Conditions\CustomCondition::class,
			'multiple' => \WPEmerge\Routing\Conditions\MultipleCondition::class,
			'negate' => \WPEmerge\Routing\Conditions\NegateCondition::class,
		] );
		$this->subject = $this->getMockForTrait( HasRoutesTrait::class );

		$this->subject->expects( $this->any() )
			->method( 'makeRoute' )
			->will( $this->returnCallback( function ( $methods, $condition, $handler ) {
				if ( ! $condition instanceof ConditionInterface ) {
					$condition = $this->condition_factory->make( $condition );
				}
				return new Route( $methods, $condition, $handler );
			} ) );
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->condition_factory );
		unset( $this->subject );
	}

	/**
	 * @covers ::getRoutes
	 * @covers ::addRoute
	 */
	public function testAddRoute() {
		$route = Mockery::mock( RouteInterface::class );
		$expected = [$route];

		$this->subject->addRoute( $route );
		$this->assertSame( $expected, $this->subject->getRoutes() );
	}

	/**
	 * @covers ::route
	 */
	public function testRoute() {
		$methods = ['GET', 'POST'];
		$condition = new UrlCondition( '/foo/bar/' );
		$handler = Mockery::mock( Handler::class );

		$route = $this->subject->route( $methods, $condition, $handler );

		$this->assertSame( $methods, $route->getMethods() );
		$this->assertSame( $condition, $route->getCondition() );
		$this->assertSame( $handler, $route->getHandler() );
		$this->assertSame( [$route], $this->subject->getRoutes() );
	}

	/**
	 * @covers ::get
	 * @covers ::post
	 * @covers ::put
	 * @covers ::patch
	 * @covers ::delete
	 * @covers ::options
	 * @covers ::any
	 */
	public function testMethods() {
		$condition = '/foo/';

		$route = $this->subject->get( $condition, Mockery::mock( Handler::class ) );
		$this->assertEquals( ['GET', 'HEAD'], $route->getMethods() );

		$route = $this->subject->post( $condition, Mockery::mock( Handler::class ) );
		$this->assertEquals( ['POST'], $route->getMethods() );

		$route = $this->subject->put( $condition, Mockery::mock( Handler::class ) );
		$this->assertEquals( ['PUT'], $route->getMethods() );

		$route = $this->subject->patch( $condition, Mockery::mock( Handler::class ) );
		$this->assertEquals( ['PATCH'], $route->getMethods() );

		$route = $this->subject->delete( $condition, Mockery::mock( Handler::class ) );
		$this->assertEquals( ['DELETE'], $route->getMethods() );

		$route = $this->subject->options( $condition, Mockery::mock( Handler::class ) );
		$this->assertEquals( ['OPTIONS'], $route->getMethods() );

		$route = $this->subject->any( $condition, Mockery::mock( Handler::class ) );
		$this->assertEquals( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route->getMethods() );
	}
}
