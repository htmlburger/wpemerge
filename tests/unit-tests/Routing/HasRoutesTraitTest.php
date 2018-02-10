<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Requests\Request;
use WPEmerge\Routing\Conditions\Url;
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
		$target = new Url( '/foo/bar/' );
		$handler = function() {};

		$route = $this->subject->route( $methods, $target, $handler );

		$this->assertSame( $methods, $route->getMethods() );
		$this->assertSame( $target, $route->getTarget() );
		$this->assertSame( $handler, $route->getHandler()->get()->get() );
		$this->assertSame( [$route], $this->subject->getRoutes() );
	}

	/**
	 * @covers ::route
	 */
	public function testRoute_NoHandler_WordPressHandler() {
		$methods = ['GET', 'POST'];
		$target = new Url( '/foo/bar/' );

		$route = $this->subject->route( $methods, $target );

		$this->assertEquals( WordPressController::class, $route->getHandler()->get()->get()['class'] );
	}

	/**
	 * @covers ::group
	 */
	public function testGroup() {
		$target = '/foo/';

		$group = $this->subject->group( $target, function( $group ) {

		} );

		$this->assertSame( [$group], $this->subject->getRoutes() );
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
		$target = '/foo/';

		$route1 = $this->subject->get( $target );
		$this->assertEquals( ['GET', 'HEAD'], $route1->getMethods() );

		$route2 = $this->subject->post( $target );
		$this->assertEquals( ['POST'], $route2->getMethods() );

		$route3 = $this->subject->put( $target );
		$this->assertEquals( ['PUT'], $route3->getMethods() );

		$route4 = $this->subject->patch( $target );
		$this->assertEquals( ['PATCH'], $route4->getMethods() );

		$route5 = $this->subject->delete( $target );
		$this->assertEquals( ['DELETE'], $route5->getMethods() );

		$route6 = $this->subject->options( $target );
		$this->assertEquals( ['OPTIONS'], $route6->getMethods() );

		$route7 = $this->subject->any( $target );
		$this->assertEquals( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route7->getMethods() );
	}
}
