<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Routing\HasRoutesTrait;
use WPEmerge\Routing\RouteInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\HasRoutesTrait
 */
class HasRoutesTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new HasRoutesTraitTestImplementation();
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->condition_factory );
		unset( $this->subject );
	}

	/**
	 * @covers ::setRoutes
	 * @covers ::getRoutes
	 */
	public function testSetRoutes() {
		$route = Mockery::mock( RouteInterface::class );

		$this->subject->setRoutes( [$route] );
		$this->assertSame( $route, $this->subject->getRoutes()[0] );
	}

	/**
	 * @covers ::addRoute
	 */
	public function testAddRoute() {
		$route1 = Mockery::mock( RouteInterface::class );
		$route2 = Mockery::mock( RouteInterface::class );

		$this->subject->setRoutes( [$route1] );
		$this->subject->addRoute( $route2 );
		$this->assertSame( $route2, $this->subject->getRoutes()[1] );
	}
}

class HasRoutesTraitTestImplementation {
	use HasRoutesTrait;
}
