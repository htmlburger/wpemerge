<?php

namespace WPEmergeTests\View;

use Mockery;
use View;
use WPEmerge\View\PhpViewEngine;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\PhpViewEngine
 */
class PhpViewEngineTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->view = View::getFacadeRoot();
		$this->viewMock = Mockery::mock()->shouldIgnoreMissing()->asUndefined();
		View::swap( $this->viewMock );

		$this->subject = new PhpViewEngine();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		View::swap( $this->view );
		unset( $this->view );
		unset( $this->viewMock );

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
	 * @covers \WPEmerge\View\PhpView::toString
	 */
	public function testMake() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';
		$expected = file_get_contents( $view );

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->andReturn( [] )
			->once();

		$this->assertEquals( $expected, $this->subject->make( [$view], [] )->toString() );
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

	/**
	 * @covers ::make
	 * @covers ::makeView
	 * @covers \WPEmerge\View\PhpView::toString
	 */
	public function testMake_View() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';
		$expected = file_get_contents( $view );

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->andReturn( [] )
			->once();

		$result = $this->subject->make( [$view], [] )->toString();

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 * @covers \WPEmerge\View\PhpView::toString
	 */
	public function testMake_ViewWithContext() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->andReturn( [] )
			->once();

		$result = $this->subject->make( [$view], ['world' => 'World'] )->toString();

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 * @covers \WPEmerge\View\PhpView::toString
	 */
	public function testMake_ViewWithGlobalContext() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-global-context.php';
		$expected = "Hello World!%wHello Global World!";

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( ['world' => 'Global World'] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->andReturn( [] )
			->once();

		$result = $this->subject->make( [$view], ['world' => 'World'] )->toString();

		$this->assertStringMatchesFormat( $expected, trim( $result ) );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 * @covers \WPEmerge\View\PhpView::toString
	 */
	public function testMake_ViewWithViewComposer() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->once();

		$result = $this->subject->make( [$view], [] )->toString();

		$this->assertTrue( true );
	}
}
