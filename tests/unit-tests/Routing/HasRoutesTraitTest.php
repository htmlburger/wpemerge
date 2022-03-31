<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge\Routing\HasRoutesTrait;
use WPEmerge\Routing\RouteInterface;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\HasRoutesTrait
 */
class HasRoutesTraitTest extends TestCase {
	public function set_up() {
		$this->subject = new HasRoutesTraitTestImplementation();
	}

	public function tear_down() {
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
	 * @covers ::addRoute
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage Attempted to register a route twice
	 */
	public function testAddRoute_SameRoute_Exception() {
		$route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();

		$this->subject->addRoute( $route );
		$this->subject->addRoute( $route );
	}

	/**
	 * @covers ::addRoute
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage The route name "foo" is already registered
	 */
	public function testAddRoute_SameRouteName_Exception() {
		$route1 = Mockery::mock( RouteInterface::class );
		$route2 = Mockery::mock( RouteInterface::class );

		$route1->shouldReceive( 'getAttribute' )
			->with( 'name' )
			->andReturn( 'foo' );

		$route2->shouldReceive( 'getAttribute' )
			->with( 'name' )
			->andReturn( 'foo' );

		$this->subject->addRoute( $route1 );
		$this->subject->addRoute( $route2 );
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
