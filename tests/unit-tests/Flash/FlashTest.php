<?php

namespace WPEmergeTests\Flash;

use ArrayAccess;
use Mockery;
use stdClass;
use WPEmerge\Flash\Flash;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Flash\Flash
 */
class FlashTest extends WP_UnitTestCase {
	/**
	 * @covers ::getStore
	 * @covers ::setStore
	 * @covers ::isValidStore
	 */
	public function testGetStore() {
		$store1 = [];
		$subject1 = new Flash( $store1 );
		$this->assertSame( $store1, $subject1->getStore() );

		$store2 = Mockery::mock( ArrayAccess::class );
		$store2->shouldReceive( 'offsetExists' )
			->andReturn( false );
		$store2->shouldReceive( 'offsetSet' );
		$store2->shouldReceive( 'offsetGet' )
			->andReturn( [] );
		$subject2 = new Flash( $store2 );
		$this->assertSame( $store2, $subject2->getStore() );
	}

	/**
	 * @covers ::setStore
	 * @covers ::isValidStore
	 */
	public function testSetStore_InvalidStore_Ignored() {
		$store = new stdClass();
		$subject = new Flash( $store );
		$this->assertNull( $subject->getStore() );
	}

	/**
	 * @covers ::enabled
	 */
	public function testEnabled() {
		$store1 = [];
		$subject1 = new Flash( $store1 );
		$this->assertTrue( $subject1->enabled() );

		$store2 = null;
		$subject2 = new Flash( $store2 );
		$this->assertFalse( $subject2->enabled() );
	}

	/**
	 * @covers ::add
	 * @covers ::addToRequest
	 * @covers ::getNext
	 * @covers ::getFromRequest
	 */
	public function testAdd() {
		$store = [];
		$subject = new Flash( $store );

		$subject->add( 'foo', 'foobar' );
		$subject->add( 'foo', ['barfoo'] );
		$subject->add( 'bar', ['barbaz', 'bazfoo'] );
		$subject->add( 'bar', 'bazbar' );

		$this->assertEquals( ['foobar', 'barfoo'], $subject->getNext( 'foo' ) );
		$this->assertEquals( ['barbaz', 'bazfoo', 'bazbar'], $subject->getNext( 'bar' ) );
		$this->assertEquals( ['foo' => ['foobar', 'barfoo'], 'bar' => ['barbaz', 'bazfoo', 'bazbar']], $subject->getNext() );
	}

	/**
	 * @covers ::addNow
	 * @covers ::addToRequest
	 * @covers ::get
	 * @covers ::getFromRequest
	 */
	public function testAddNow() {
		$store = [];
		$subject = new Flash( $store );

		$subject->addNow( 'foo', 'foobar' );
		$subject->addNow( 'foo', ['barfoo'] );
		$subject->addNow( 'bar', ['barbaz', 'bazfoo'] );
		$subject->addNow( 'bar', 'bazbar' );

		$this->assertEquals( ['foobar', 'barfoo'], $subject->get( 'foo' ) );
		$this->assertEquals( ['barbaz', 'bazfoo', 'bazbar'], $subject->get( 'bar' ) );
		$this->assertEquals( ['foo' => ['foobar', 'barfoo'], 'bar' => ['barbaz', 'bazfoo', 'bazbar']], $subject->get() );
	}

	/**
	 * @covers ::clear
	 * @covers ::clearFromRequest
	 */
	public function testClear() {
		$store = [];
		$subject = new Flash( $store );

		$subject->addNow( 'foo', 'foobar' );
		$subject->addNow( 'bar', ['barbaz', 'bazfoo'] );
		$subject->clear( 'foo' );

		$this->assertEquals( [], $subject->get( 'foo' ) );
		$this->assertNull( $subject->get( 'foo', null ) );
		$this->assertEquals( [ 'bar' => ['barbaz', 'bazfoo']], $subject->get() );

		$subject->clear();

		$this->assertEquals( [], $subject->get() );
	}

	/**
	 * @covers ::clearNext
	 * @covers ::clearFromRequest
	 */
	public function testClearNext() {
		$store = [];
		$subject = new Flash( $store );

		$subject->add( 'foo', 'foobar' );
		$subject->add( 'bar', ['barbaz', 'bazfoo'] );
		$subject->clearNext( 'foo' );

		$this->assertEquals( [], $subject->getNext( 'foo' ) );
		$this->assertNull( $subject->getNext( 'foo', null ) );
		$this->assertEquals( ['bar' => ['barbaz', 'bazfoo']], $subject->getNext() );

		$subject->clearNext();

		$this->assertEquals( [], $subject->getNext() );
	}

	/**
	 * @covers ::shift
	 */
	public function testShift() {
		$store = [];
		$subject = new Flash( $store );

		$subject->add( 'foo', 'foobar' );
		$subject->shift();

		$this->assertEquals( ['foobar'], $subject->get( 'foo' ) );
		$this->assertEquals( [], $subject->getNext( 'foo' ) );
	}

	/**
	 * @covers ::save
	 */
	public function testSave() {
		$store_key = '__foobar';
		$store = [];
		$subject = new Flash( $store, $store_key );

		$subject->add( 'foo', 'foobar' );
		$subject->save();

		$this->assertEquals( [
			$store_key => [
				Flash::CURRENT_KEY => [],
				Flash::NEXT_KEY => ['foo' => ['foobar']],
			]
		], $store );
	}

	/**
	 * @covers ::validateStore
	 */
	public function testValidateStore_Valid_DoesNotThrowException() {
		$store = [];
		$subject = new Flash( $store );

		$subject->get();

		$this->assertTrue( true );
	}

	/**
	 * @covers ::getFromRequest
	 * @covers ::validateStore
	 * @expectedException \Exception
	 * @expectedExceptionMessage without an active session
	 */
	public function testGetFromRequest_InvalidStore_ThrowException() {
		$store = new stdClass();
		$subject = new Flash( $store );
		$subject->get( 'foo' );
	}

	/**
	 * @covers ::addToRequest
	 * @covers ::validateStore
	 * @expectedException \Exception
	 * @expectedExceptionMessage without an active session
	 */
	public function testAddToRequest_InvalidStore_ThrowException() {
		$store = new stdClass();
		$subject = new Flash( $store );
		$subject->add( 'foo', 'foobar' );
	}

	/**
	 * @covers ::clearFromRequest
	 * @covers ::validateStore
	 * @expectedException \Exception
	 * @expectedExceptionMessage without an active session
	 */
	public function testClearFromRequest_InvalidStore_ThrowException() {
		$store = new stdClass();
		$subject = new Flash( $store );
		$subject->clear( 'foo' );
	}

	/**
	 * @covers ::shift
	 * @covers ::validateStore
	 * @expectedException \Exception
	 * @expectedExceptionMessage without an active session
	 */
	public function testShift_InvalidStore_ThrowException() {
		$store = new stdClass();
		$subject = new Flash( $store );
		$subject->shift();
	}

	/**
	 * @covers ::save
	 * @covers ::validateStore
	 * @expectedException \Exception
	 * @expectedExceptionMessage without an active session
	 */
	public function testSave_InvalidStore_ThrowException() {
		$store = new stdClass();
		$subject = new Flash( $store );
		$subject->save();
	}
}
