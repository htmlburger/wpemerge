<?php

use Obsidian\Request;
use Obsidian\Input\OldInput;

/**
 * @coversDefaultClass \Obsidian\Input\OldInput
 */
class OldInputTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		session_start();
		$this->subject = new OldInput();
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->subject );
		session_destroy();
	}

	/**
	 * @covers ::all
	 * @covers ::store
	 */
	public function testAll() {
		$expected = ['foo' => 'bar', 'bar'=>'baz'];

		$this->subject->store( $expected );
		$this->assertEquals( $expected, $this->subject->all() );
	}

	/**
	 * @covers ::all
	 * @covers ::store
	 */
	public function testStore_CalledTwice_MergedResults() {
		$part1 = ['foo' => 'bar'];
		$part2 = ['bar' => 'baz'];
		$expected = array_merge( $part1, $part2 );

		$this->subject->store( $part1 );
		$this->subject->store( $part2 );
		$this->assertEquals( $expected, $this->subject->all() );
	}

	/**
	 * @covers ::all
	 * @covers ::clear
	 */
	public function testClear() {
		$data = ['foo' => 'bar', 'bar'=>'baz'];
		$expected = [];

		$this->subject->store( $data );
		$this->subject->clear();
		$this->assertEquals( $expected, $this->subject->all() );
	}

	/**
	 * @covers ::get
	 */
	public function testGet_ExistingKey_ReturnValue() {
		$data = ['foo' => 'bar', 'bar'=>'baz'];
		$expected = 'baz';

		$this->subject->store( $data );
		$this->assertEquals( $expected, $this->subject->get( 'bar' ) );
	}

	/**
	 * @covers ::get
	 */
	public function testGet_NonexistantKey_ReturnDefault() {
		$expected = 'foobar';

		$this->assertEquals( $expected, $this->subject->get( 'nonexistantKey', $expected ) );
	}
}
