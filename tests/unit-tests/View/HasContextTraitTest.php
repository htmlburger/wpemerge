<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\View\HasContextTrait;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\HasContextTrait
 */
class HasContextTraitTest extends WP_UnitTestCase {
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
