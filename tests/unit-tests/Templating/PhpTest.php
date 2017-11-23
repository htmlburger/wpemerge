<?php

use Obsidian\Templating\Php as PhpEngine;

/**
 * @coversDefaultClass \Obsidian\Templating\Php
 */
class PhpTest extends WP_UnitTestCase {
	/**
	 * @covers ::render
	 */
	public function testRender_Template_Rendered() {
		$template = OBSIDIAN_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'template.php';
		$expected = file_get_contents( $template );

		$subject = new PhpEngine();
		$result = $subject->render( $template, [] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}

	/**
	 * @covers ::render
	 */
	public function testRender_TemplateWithVariables_Rendered() {
		$template = OBSIDIAN_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'template-with-context.php';
		$expected = 'Hello World!';

		$subject = new PhpEngine();
		$result = $subject->render( $template, ['world' => 'World'] );

		$this->assertEquals( trim( $expected ), trim( $result ) );
	}
}
