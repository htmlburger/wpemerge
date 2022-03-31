<?php

namespace WPEmergeTests\Application;

use Mockery;
use Pimple\Container;
use WPEmerge\Application\GenericFactory;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\GenericFactory
 */
class GenericFactoryTest extends TestCase {
	public function set_up() {
		$this->container = Mockery::mock( Container::class );
		$this->subject = new GenericFactory( $this->container );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->container );
		unset( $this->subject );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_UnknownClass_CreateFreshInstance() {
		$class = \WPEmergeTestTools\TestService::class;

		$this->container->shouldReceive( 'offsetExists' )
			->with( $class )
			->andReturn( false );

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
	public function testMake_UnknownNonexistentClass_Exception() {
		$class = \WPEmergeTestTools\NonExistentClass::class;

		$this->container->shouldReceive( 'offsetExists' )
			->with( $class )
			->andReturn( false );

		$this->subject->make( $class );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_KnownClass_ResolveInstanceFromContainer() {
		$expected = 'foo';
		$class = \WPEmergeTestTools\TestService::class;

		$this->container->shouldReceive( 'offsetExists' )
			->with( $class )
			->andReturn( true );

		$this->container->shouldReceive( 'offsetGet' )
			->andReturnUsing( function ( $class ) use ( $expected ) {
				$instance = new $class();
				$instance->setTest( $expected );
				return $instance;
			} );

		$instance = $this->subject->make( $class );

		$this->assertEquals( $expected, $instance->getTest() );
	}
}
