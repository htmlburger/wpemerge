<?php

namespace WPEmergeTests\Input;

use Mockery;
use WPEmerge\Flash\Flash;
use WPEmerge\Input\OldInput;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Input\OldInput
 */
class OldInputTest extends TestCase {
	public function set_up() {
		$this->flash = Mockery::mock( Flash::class );
		$this->flash_key = '__foobar';
		$this->subject = new OldInput( $this->flash, $this->flash_key );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->subject );
		unset( $this->flash );
	}

	/**
	 * @covers ::enabled
	 */
	public function testEnabled() {
		$flash1 = Mockery::mock( Flash::class );
		$flash1->shouldReceive( 'enabled' )
			->andReturn( true );
		$subject1 = new OldInput( $flash1 );

		$this->assertTrue( $subject1->enabled() );

		$flash2 = Mockery::mock( Flash::class );
		$flash2->shouldReceive( 'enabled' )
			->andReturn( false );
		$subject2 = new OldInput( $flash2 );

		$this->assertFalse( $subject2->enabled() );
	}

	/**
	 * @covers ::get
	 */
	public function testGet() {
		$this->flash->shouldReceive( 'get' )
			->with( $this->flash_key, [] )
			->andReturn( ['foo' => 'foobar'] );

		$this->assertEquals( 'foobar', $this->subject->get( 'foo' ) );
		$this->assertEquals( 'barbaz', $this->subject->get( 'bar', 'barbaz' ) );
	}

	/**
	 * @covers ::set
	 */
	public function testSet() {
		$this->flash->shouldReceive( 'add' )
			->with( $this->flash_key, ['foo' => 'foobar'] )
			->once();

		$this->subject->set( ['foo' => 'foobar'] );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::clear
	 */
	public function testClear() {
		$this->flash->shouldReceive( 'clear' )
			->with( $this->flash_key )
			->once();

		$this->subject->clear();

		$this->assertTrue( true );
	}
}
