<?php

namespace WPEmergeTests\Routing\Conditions;

use Mockery;
use WPEmerge\Routing\Conditions\CustomCondition;
use WPEmerge\Routing\Conditions\MultipleCondition;
use WPEmerge\Requests\Request;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Conditions\MultipleCondition
 */
class MultipleConditionTest extends WP_UnitTestCase {
	/**
	 * @covers ::__construct
	 * @covers ::getConditions
	 */
	public function testConstruct() {
		$condition1 = new CustomCondition( '__return_true' );
		$condition2 = function() { return false; };
		$request = Mockery::mock( Request::class )->shouldIgnoreMissing();

		$subject = new MultipleCondition( [$condition1, $condition2] );

		$this->assertEquals( [$condition1, new CustomCondition( $condition2 )], $subject->getConditions() );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$condition1 = new CustomCondition( '__return_true' );
		$condition2 = new CustomCondition( '__return_false' );
		$request = Mockery::mock( Request::class )->shouldIgnoreMissing();

		$subject1 = new MultipleCondition( [$condition1] );
		$this->assertTrue( $subject1->isSatisfied( $request ) );

		$subject2 = new MultipleCondition( [$condition2] );
		$this->assertFalse( $subject2->isSatisfied( $request ) );

		$subject3 = new MultipleCondition( [$condition1, $condition2] );
		$this->assertFalse( $subject3->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments() {
		$condition1 = new CustomCondition( '__return_true', 'custom_arg_1', 'custom_arg_2' );
		$condition2 = [function() { return false; }, 'custom_arg_3'];
		$request = Mockery::mock( Request::class )->shouldIgnoreMissing();

		$subject = new MultipleCondition( [$condition1, $condition2] );

		$this->assertEquals( ['custom_arg_1', 'custom_arg_2', 'custom_arg_3'], $subject->getArguments( $request ) );
	}
}
