<?php

namespace WPEmergeTests\View;

use WPEmerge\View\HasNameTrait;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\View\HasNameTrait
 */
class HasNameTraitTest extends TestCase {
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
