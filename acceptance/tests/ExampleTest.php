<?php
/**
 * Example test class
 *
 * @package wpacceptance
 */

/**
 * PHPUnit test class
 */
class ExampleTest extends \WPAcceptance\PHPUnit\TestCase {
	public function testHome() {
		$I = $this->openBrowserPage();
		$I->moveTo('/');
		$I->seeText('acceptance:index');
	}

	public function testGet() {
		$I = $this->openBrowserPage();
		$I->moveTo('/hook');
		$I->seeText('acceptance:/hook');
	}
}
