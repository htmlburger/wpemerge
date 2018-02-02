<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\View\PhpView;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\PhpView
 */
class PhpViewTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new PhpView();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::getName
	 * @covers ::setName
	 */
	public function testGetName() {
		$expected = 'foo';
		$this->subject->setName( $expected );
		$this->assertEquals( $expected, $this->subject->getName() );
	}

	/**
	 * @covers ::getName
	 * @covers ::setName
	 */
	public function testGetFilepath() {
		$expected = 'foo';
		$this->subject->setFilepath( $expected );
		$this->assertEquals( $expected, $this->subject->getFilepath() );
	}

	/**
	 * @covers ::toString
	 * @expectedException \Exception
	 * @expectedExceptionMessage must have a name
	 */
	public function testToString_WithoutName() {
		$this->subject->toString();
	}

	/**
	 * @covers ::toString
	 * @expectedException \Exception
	 * @expectedExceptionMessage must have a filepath
	 */
	public function testToString_WithoutFilepath() {
		$this->subject->setName( 'foo' );
		$this->subject->toString();
	}
}
