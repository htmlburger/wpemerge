<?php

namespace WPEmergeTests\Application;

use Mockery;
use WPEmerge\Application\ClosureFactory;
use WPEmerge\Application\GenericFactory;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\ClosureFactory
 */
class ClosureFactoryTest extends TestCase {
	public function set_up() {
		$this->generic_factory = Mockery::mock( GenericFactory::class );
		$this->subject = new ClosureFactory( $this->generic_factory );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->generic_factory );
		unset( $this->subject );
	}

	/**
	 * @covers ::value
	 */
	public function testValue() {
		$key = 'foo';
		$expected = 'bar';

		$this->generic_factory->shouldReceive( 'make' )
			->with( $key )
			->andReturn( $expected );

		$closure = $this->subject->value( $key );
		$this->assertEquals( $expected, $closure() );
	}

	/**
	 * @covers ::method
	 */
	public function testMethod() {
		$key = 'foo';
		$method = 'add';
		$a = 1;
		$b = 2;
		$expected = $a + $b;

		$this->generic_factory->shouldReceive( 'make' )
			->with( $key )
			->andReturn( new ClosureFactoryTestInstance() );

		$closure = $this->subject->method( $key, $method );
		$this->assertEquals( $expected, $closure( $a, $b ) );
	}
}

class ClosureFactoryTestInstance {
	public function add( $a, $b ) {
		return $a + $b;
	}
}
