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
	 * @covers ::addRoute
	 */
	public function testAddRoute() {
		$route1 = Mockery::mock( RouteInterface::class );
		$route2 = Mockery::mock( RouteInterface::class );

		$route1->shouldReceive( 'getAttribute' )
			->with( 'name' )
			->andReturn( '' );

		$route2->shouldReceive( 'getAttribute' )
			->with( 'name' )
			->andReturn( '' );

		$this->subject->addRoute( $route1 );
		$this->subject->addRoute( $route2 );
		$this->assertSame( $route2, $this->subject->getRoutes()[1] );
	}

	/**
	 * @covers ::removeRoute
	 */
	public function testRemoveRoute() {
		$route = Mockery::mock( RouteInterface::class );

		$route->shouldReceive( 'getAttribute' )
			->with( 'name' )
			->andReturn( '' );

		$this->subject->removeRoute( $route );
		$this->assertEquals( [], $this->subject->getRoutes() );
		$this->subject->addRoute( $route );
		$this->assertEquals( [$route], $this->subject->getRoutes() );
		$this->subject->removeRoute( $route );
		$this->assertEquals( [], $this->subject->getRoutes() );
	}
}

class HasRoutesTraitTestImplementation {
	use HasRoutesTrait;
}
