<?php

namespace WPEmergeTests\Routing\Conditions;

use Mockery;
use WPEmerge\Routing\Conditions\CustomCondition;
use WPEmerge\Requests\Request;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Conditions\CustomCondition
 */
class CustomConditionTest extends WP_UnitTestCase {
	/**
	 * @covers ::__construct
	 * @covers ::getCallable
	 * @covers ::getArguments
	 */
	public function testConstruct() {
		$callable = function() {};
		$arguments = ['foo', 'bar'];
		$request = Mockery::mock( Request::class )->shouldIgnoreMissing();

		$subject = new CustomCondition( $callable, $arguments[0], $arguments[1] );

		$this->assertSame( $callable, $subject->getCallable() );
		$this->assertEquals( $arguments, $subject->getArguments( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( Request::class )->shouldIgnoreMissing();

		$subject1 = new CustomCondition( '__return_true' );
		$this->assertTrue( $subject1->isSatisfied( $request ) );

		$subject2 = new CustomCondition( '__return_false' );
		$this->assertFalse( $subject2->isSatisfied( $request ) );
	}
}
