<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\Helpers\MixedType;
use WPEmerge\View\PhpViewEngine;
use WPEmerge\View\PhpViewFilesystemFinder;
use WPEmerge\View\ViewInterface;
use WPEmergeTestTools\Helper;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\PhpViewEngine
 */
class PhpViewEngineTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$finder = new PhpViewFilesystemFinder( [] );
		$this->subject = new PhpViewEngine( $finder );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::exists
	 */
	public function testExists() {
		$this->assertTrue( $this->subject->exists( WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view.php' ) );
		$this->assertTrue( $this->subject->exists( 'index.php' ) );
		$this->assertTrue( $this->subject->exists( 'index' ) );
		$this->assertFalse( $this->subject->exists( 'nonexistant' ) );
		$this->assertFalse( $this->subject->exists( '' ) );
	}

	/**
	 * @covers ::canonical
	 */
	public function testCanonical() {
		$expected = realpath( MixedType::normalizePath( locate_template( 'index.php', false ) ) );

		$this->assertEquals( $expected, $this->subject->canonical( $expected ) );
		$this->assertEquals( $expected, $this->subject->canonical( 'index.php' ) );
		$this->assertEquals( $expected, $this->subject->canonical( 'index' ) );
		$this->assertEquals( '', $this->subject->canonical( 'nonexistant' ) );
		$this->assertEquals( '', $this->subject->canonical( '' ) );
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
