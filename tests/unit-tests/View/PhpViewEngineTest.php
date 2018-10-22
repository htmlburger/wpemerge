<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\View\PhpViewEngine;
use WPEmerge\View\ViewInterface;
use WPEmergeTestTools\Helper;
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
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view.php';

		$this->assertTrue( $this->subject->exists( $index ) );
		$this->assertTrue( $this->subject->exists( $view ) );
		$this->assertFalse( $this->subject->exists( '' ) );
	}

	/**
	 * @covers ::canonical
	 */
	public function testCanonical() {
		$root = realpath( TEMPLATEPATH ) . DIRECTORY_SEPARATOR;
		$full = realpath( locate_template( 'index.php' ) );
		$expected = substr( $full, strlen( $root ) );

		$this->assertEquals( '', $this->subject->canonical( '' ) );
		$this->assertEquals( $expected, $this->subject->canonical( 'index' ) );
		$this->assertEquals( $expected, $this->subject->canonical( 'index.php' ) );
		$this->assertEquals( $expected, $this->subject->canonical( $full ) );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 */
	public function testMake_View() {
		$file = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view.php';
		$view = $this->subject->make( [$file] );

		$this->assertInstanceOf( ViewInterface::class, $view );
		$this->assertEquals( $file, $view->getName() );
		$this->assertEquals( $file, $view->getFilepath() );
	}

	/**
	 * @covers ::makeView
	 * @covers ::getViewLayout
	 */
	public function testMake_WithLayout() {
		list( $view, $layout, $output, $handle ) = Helper::createLayoutView();
		$view = $this->subject->make( [$view] );

		$this->assertEquals( $layout, $view->getLayout()->getFilepath() );

		Helper::deleteLayoutView( $handle );
	}

	/**
	 * @covers ::getViewLayout
	 * @expectedException \WPEmerge\View\ViewNotFoundException
	 * @expectedExceptionMessage View layout not found
	 */
	public function testMake_WithIncorrectLayout() {
		// Rely on the fact that view-with-layout.php uses a sprintf() token instead of a real path so it fails.
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view-with-layout.php';
		$view = $this->subject->make( [$view] );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 * @expectedException \WPEmerge\View\ViewNotFoundException
	 * @expectedExceptionMessage View not found
	 */
	public function testMake_NoView() {
		$this->subject->make( [''], [] );
	}
}
