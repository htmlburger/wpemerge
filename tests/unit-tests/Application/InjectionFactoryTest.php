<?php

namespace WPEmergeTests\Application;

use Mockery;
use WPEmerge\Application\Application;
use WPEmerge\Application\InjectionFactory;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\InjectionFactory
 */
class InjectionFactoryTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->app = Mockery::mock( Application::class );
		$this->subject = new InjectionFactory( $this->app );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->app );
		unset( $this->subject );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_UnknownClass_CreateFreshInstance() {
		$class = \WPEmergeTestTools\TestService::class;

		$this->app->shouldReceive( 'resolve' )
			->andReturn( null );

		$instance1 = $this->subject->make( $class );
		$instance2 = $this->subject->make( $class );

		$this->assertInstanceOf( $class, $instance1 );
		$this->assertInstanceOf( $class, $instance2 );
		$this->assertNotSame( $instance1, $instance2 );
	}

	/**
	 * @covers ::make
	 * @expectedException \WPEmerge\Exceptions\ClassNotFoundException
	 * @expectedExceptionMessage Class not found
	 */
	public function testMake_UnknownNonexistantClass_Exception() {
		$class = \WPEmergeTestTools\NonExistantClass::class;

		$this->app->shouldReceive( 'resolve' )
			->andReturn( null );

		$this->subject->make( $class );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_KnownClass_ResolveInstanceFromContainer() {
		$expected = 'foo';
		$class = \WPEmergeTestTools\TestService::class;

		$this->app->shouldReceive( 'resolve' )
			->andReturnUsing( function ( $class ) use ( $expected ) {
				$instance = new $class();
				$instance->setTest( $expected );
				return $instance;
			} );

		$instance = $this->subject->make( $class );

		$this->assertEquals( $expected, $instance->getTest() );
	}
}
