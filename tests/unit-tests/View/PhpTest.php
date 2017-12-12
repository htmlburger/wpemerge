<?php

namespace WPEmergeTests\View;

use Mockery;
use View;
use WPEmerge;
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

		$result = $this->subject->render( $view, [] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::render
	 */
	public function testRender_ViewWithVariables_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$result = $this->subject->render( $view, ['world' => 'World'] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::render
	 * @covers ::__construct
	 */
	public function testRender_GlobalContext_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-global-context.php';
		$expected = "Hello World!%wHello Global World!";

		$this->viewMock->shouldReceive( 'getGlobalContext' )
			->andReturn( ['world' => 'Global World'] )
			->once();

		$result = $this->subject->render( $view, ['world' => 'World'] );

		$this->assertStringMatchesFormat( $expected, trim( $result ) );
	}
}
