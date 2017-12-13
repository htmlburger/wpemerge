<?php

namespace WPEmergeTests\View;

use Mockery;
use View;
use WPEmerge\View\Php as PhpEngine;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\Php
 */
class PhpTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->view = View::getFacadeRoot();
		$this->viewMock = Mockery::mock()->shouldIgnoreMissing()->asUndefined();
		View::swap( $this->viewMock );

		$this->subject = new PhpEngine();
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
	 * @covers ::render
	 */
	public function testRender_View_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';
		$expected = file_get_contents( $view );

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->with( $view )
			->andReturn( [] )
			->once();

		$result = $this->subject->render( $view, [] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::render
	 */
	public function testRender_ViewWithVariables_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->with( $view )
			->andReturn( [] )
			->once();

		$result = $this->subject->render( $view, ['world' => 'World'] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::render
	 */
	public function testRender_GlobalContext_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-global-context.php';
		$expected = "Hello World!%wHello Global World!";

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( ['world' => 'Global World'] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->with( $view )
			->andReturn( [] )
			->once();

		$result = $this->subject->render( $view, ['world' => 'World'] );

		$this->assertStringMatchesFormat( $expected, trim( $result ) );
	}

	/**
	 * @covers ::render
	 */
	public function testRender_WithViewComposer_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = "Hello Composer World!";

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->with( $view )
			->andReturn( ['world' => 'Composer World'] )
			->once();

		$result = $this->subject->render( $view, [] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}
}
