<?php

namespace WPEmergeTests\Routing\Conditions;

use Mockery;
use WPEmerge\Routing\Conditions\CustomCondition;
use WPEmerge\Requests\RequestInterface;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Conditions\CustomCondition
 */
class CustomConditionTest extends TestCase {
	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();

		$subject1 = new CustomCondition( '__return_true' );
		$this->assertTrue( $subject1->isSatisfied( $request ) );

		$subject2 = new CustomCondition( '__return_false' );
		$this->assertFalse( $subject2->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments() {
		$callable = function() {};
		$arguments = ['foo', 'bar'];
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();

		$subject = new CustomCondition( $callable, $arguments[0], $arguments[1] );

		$this->assertSame( $callable, $subject->getCallable() );
		$this->assertEquals( $arguments, $subject->getArguments( $request ) );
	}
}
