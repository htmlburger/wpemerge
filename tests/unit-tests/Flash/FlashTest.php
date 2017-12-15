<?php

namespace WPEmergeTests\Flash;

use stdClass;
use WPEmerge\Flash\Flash;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Flash\Flash
 */
class FlashTest extends WP_UnitTestCase {
	/**
	 * @covers ::__construct
	 * @covers ::getStorage
	 */
	public function testConstruct() {
		$expected = array();
		$subject = new Flash( $expected );
		$this->assertSame( $expected, $subject->getStorage() );
	}

	/**
	 * @covers ::setStorage
	 * @covers ::getStorage
	 * @covers ::isValidStorage
	 */
	public function testSetStorage_ValidStorage_Assigned() {
		$expected = array();
		$initial_storage = array();

		$subject = new Flash( $initial_storage );
		$subject->setStorage( $expected );

		$this->assertSame( $expected, $subject->getStorage() );
	}

	/**
	 * @covers ::setStorage
	 * @covers ::getStorage
	 * @covers ::isValidStorage
	 */
	public function testSetStorage_InvalidStorage_Ignored() {
		$expected = array();
		$invalid_storage = new stdClass();

		$subject = new Flash( $expected );
		$subject->setStorage( $invalid_storage );

		$this->assertSame( $expected, $subject->getStorage() );
	}

	/**
	 * @covers ::enabled
	 */
	public function testEnabled() {
		$expected1 = false;
		$storage1 = null;
		$subject1 = new Flash( $storage1 );
		$this->assertEquals( $expected1, $subject1->enabled() );

		$expected2 = true;
		$storage2 = [];
		$subject2 = new Flash( $storage2 );
		$this->assertEquals( $expected2, $subject2->enabled() );
	}

	/**
	 * @covers ::peek
	 * @covers ::validateStorage
	 * @covers ::isValidStorage
	 * @expectedException \Exception
	 * @expectedExceptionMessage without an active session
	 */
	public function testPeek_InvalidStorage_ThrowsException() {
		$invalid_storage = new stdClass();
		$subject = new Flash( $invalid_storage );
		$subject->peek( 'foobar' );
	}

	/**
	 * @covers ::add
	 * @covers ::peek
	 * @covers ::validateStorage
	 */
	public function testPeek_ExistingKey_ReturnValue() {
		$expected = ['foo'];
		$key = 'key';
		$storage = [];

		$subject = new Flash( $storage );
		$subject->add( $key, $expected );

		$this->assertEquals( $expected, $subject->peek( $key ) );
	}

	/**
	 * @covers ::peek
	 * @covers ::validateStorage
	 */
	public function testPeek_NonExistantKey_ReturnEmptyArray() {
		$expected = [];
		$key = 'key';
		$storage = [];

		$subject = new Flash( $storage );

		$this->assertEquals( $expected, $subject->peek( $key ) );
	}

	/**
	 * @covers ::add
	 * @covers ::peek
	 * @covers ::validateStorage
	 */
	public function testPeek_StringValue_ReturnValueInArray() {
		$expected_value = 'foo';
		$expected = [$expected_value];
		$key = 'key';
		$storage = [];

		$subject = new Flash( $storage );
		$subject->add( $key, $expected_value );

		$this->assertEquals( $expected, $subject->peek( $key ) );
	}

	/**
	 * @covers ::add
	 * @covers ::get
	 * @covers ::validateStorage
	 */
	public function testGet_ExistingKey_ReturnValueAndClear() {
		$expected1 = ['foo'];
		$expected2 = [];
		$key = 'key';
		$storage = [];

		$subject = new Flash( $storage );
		$subject->add( $key, $expected1 );

		$this->assertEquals( $expected1, $subject->get( $key ) );
		$this->assertEquals( $expected2, $subject->get( $key ) );
	}

	/**
	 * @covers ::add
	 * @covers ::peek
	 * @covers ::validateStorage
	 */
	public function testAdd_CalledMultipledTimes_ReturnArrayOfValues() {
		$expected = ['foo', 'bar', 'baz'];
		$key = 'key';
		$storage = [];

		$subject = new Flash( $storage );
		$subject->add( $key, $expected[0] );
		$subject->add( $key, $expected[1] );
		$subject->add( $key, $expected[2] );

		$this->assertEquals( $expected, $subject->peek( $key ) );
	}

	/**
	 * @covers ::add
	 * @covers ::peek
	 * @covers ::validateStorage
	 */
	public function testAdd_CalledWithDifferentKeys_StoreNestedArray() {
		$expected = ['key1' => ['foo'], 'key2' => ['bar']];
		$values = array_values( $expected );
		$keys = array_keys( $expected );
		$storage = [];

		$subject = new Flash( $storage );
		$subject->add( $keys[0], $values[0][0] );
		$subject->add( $keys[1], $values[1][0] );

		$this->assertEquals( $expected, $subject->peek() );
	}

	/**
	 * @covers ::peek
	 * @covers ::clear
	 * @covers ::validateStorage
	 */
	public function testClear_WithKey_ClearKey() {
		$expected = ['key1' => [], 'key2'=>['bar']];
		$storage = [];

		$subject = new Flash( $storage );
		$subject->add( 'key1', 'foo' );
		$subject->add( 'key2', 'bar' );
		$subject->clear( 'key1' );

		$this->assertEquals( $expected, $subject->peek() );
	}

	/**
	 * @covers ::peek
	 * @covers ::clear
	 * @covers ::validateStorage
	 */
	public function testClear_WithoutKey_ClearAll() {
		$expected = ['key1' => [], 'key2'=>[]];
		$storage = [];

		$subject = new Flash( $storage );
		$subject->add( 'key1', 'foo' );
		$subject->add( 'key2', 'bar' );
		$subject->clear();

		$this->assertEquals( $expected, $subject->peek() );
	}
}
