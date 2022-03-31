<?php

namespace WPEmergeTestTools;

use WP_UnitTest_Factory;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as TestCasePolyfill;

class TestCase extends TestCasePolyfill {
	/**
	 * Get a WP_UnitTest_Factory instance.
	 *
	 * @return WP_UnitTest_Factory
	 */
	public function wp_factory() {
		static $wp_factory = null;

		if ( ! $wp_factory ) {
			$wp_factory = new WP_UnitTest_Factory();
		}

		return $wp_factory;
	}
}
