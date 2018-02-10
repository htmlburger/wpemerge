<?php

namespace WPEmergeTests\View;

use WPEmerge\View\HasNameTrait;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\HasNameTrait
 */
class HasNameTraitTest extends WP_UnitTestCase {
	/**
	 * @covers ::getName
	 * @covers ::setName
	 */
	public function testGetNameContext() {
		$subject = $this->getMockForTrait( HasNameTrait::class );
		$expected = 'foo';

		$subject->setName( $expected );
		$this->assertEquals( $expected, $subject->getName() );
	}
}
