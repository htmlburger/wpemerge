<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\View\PhpViewEngine;
use WPEmerge\View\ViewInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\PhpViewEngine
 */
class PhpViewEngineTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new PhpViewEngine();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::exists
	 * @covers ::resolveFilepath
	 * @covers ::resolveFilepathFromFilesystem
	 */
	public function testExists() {
		$index = 'index.php';
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';

		$this->assertTrue( $this->subject->exists( $index ) );
		$this->assertTrue( $this->subject->exists( $view ) );
		$this->assertFalse( $this->subject->exists( '' ) );
	}

	/**
	 * @covers ::canonical
	 */
	public function testCanonical() {
		$expected = realpath( locate_template( 'index.php' ) );

		$this->assertEquals( $expected, $this->subject->canonical( 'index' ) );
		$this->assertEquals( $expected, $this->subject->canonical( 'index.php' ) );
		$this->assertEquals( '', $this->subject->canonical( '' ) );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 */
	public function testMake_View() {
		$file = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';
		$view = $this->subject->make( [$file] );

		$this->assertInstanceOf( ViewInterface::class, $view );
		$this->assertEquals( $file, $view->getName() );
		$this->assertEquals( $file, $view->getFilepath() );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 * @expectedException \Exception
	 * @expectedExceptionMessage View not found
	 */
	public function testMake_NoView() {
		$this->subject->make( [''], [] );
	}
}
