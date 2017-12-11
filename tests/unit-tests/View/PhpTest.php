<?php

namespace WPEmergeTests\View;

use WPEmerge\View\Php as PhpEngine;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\Php
 */
class PhpTest extends WP_UnitTestCase {
	/**
	 * @covers ::render
	 */
	public function testRender_View_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';
		$expected = file_get_contents( $view );

		$subject = new PhpEngine( [] );
		$result = $subject->render( $view, [] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::render
	 */
	public function testRender_ViewWithVariables_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$subject = new PhpEngine( [] );
		$result = $subject->render( $view, ['world' => 'World'] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::render
	 * @covers ::__construct
	 */
	public function testRender_GlobalContext_Rendered() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-global-context.php';
		$expected = "Hello World!%wHello Global World!";

		$subject = new PhpEngine( ['world' => 'Global World'] );
		$result = $subject->render( $view, ['world' => 'World'] );

		$this->assertStringMatchesFormat( $expected, trim( $result ) );
	}
}
