<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Routing\Conditions\UrlCondition;
use WPEmerge\Routing\HasRoutesTrait;
use WPEmerge\Routing\RouteInterface;
use WPEmerge\Controllers\WordPressController;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\HasRoutesTrait
 */
class HasRoutesTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = $this->getMockForTrait( HasRoutesTrait::class );
	}

	public function tearDown() {
		parent::tearDown();

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
		$handler = function() {};

		$route = $this->subject->route( $methods, $condition, $handler );

		$this->assertSame( $methods, $route->getMethods() );
		$this->assertSame( $condition, $route->getCondition() );
		$this->assertSame( $handler, $route->getPipeline()->getHandler()->get()->get() );
		$this->assertSame( [$route], $this->subject->getRoutes() );
	}

	/**
	 * @covers ::route
	 */
	public function testRoute_NoHandler_WordPressHandler() {
		$methods = ['GET', 'POST'];
		$condition = new UrlCondition( '/foo/bar/' );

		$route = $this->subject->route( $methods, $condition );

		$this->assertEquals( WordPressController::class, $route->getPipeline()->getHandler()->get()->get()['class'] );
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

		$route1 = $this->subject->get( $condition );
		$this->assertEquals( ['GET', 'HEAD'], $route1->getMethods() );

		$route2 = $this->subject->post( $condition );
		$this->assertEquals( ['POST'], $route2->getMethods() );

		$route3 = $this->subject->put( $condition );
		$this->assertEquals( ['PUT'], $route3->getMethods() );

		$route4 = $this->subject->patch( $condition );
		$this->assertEquals( ['PATCH'], $route4->getMethods() );

		$route5 = $this->subject->delete( $condition );
		$this->assertEquals( ['DELETE'], $route5->getMethods() );

		$route6 = $this->subject->options( $condition );
		$this->assertEquals( ['OPTIONS'], $route6->getMethods() );

		$route7 = $this->subject->any( $condition );
		$this->assertEquals( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route7->getMethods() );
	}
}
