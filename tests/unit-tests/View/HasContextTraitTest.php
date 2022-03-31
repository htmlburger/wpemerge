<?php

namespace WPEmergeTests\View;

use WPEmerge\View\HasContextTrait;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\View\HasContextTrait
 */
class HasContextTraitTest extends TestCase {
	/**
	 * @covers ::getContext
	 * @covers ::with
	 */
	public function testGetContext() {
		$subject = $this->getMockForTrait( HasContextTrait::class );

		$subject->with( 'foo', 'foobar' );
		$subject->with( [
			'bar' => 'barbar',
			'baz' => 'bazbar',
		] );

		$this->assertEquals( 'foobar', $subject->getContext( 'foo' ) );

		$this->assertEquals( [
			'foo' => 'foobar',
			'bar' => 'barbar',
			'baz' => 'bazbar',
		], $subject->getContext() );
	}
}
