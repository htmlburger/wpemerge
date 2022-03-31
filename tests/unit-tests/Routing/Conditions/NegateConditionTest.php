<?php

namespace WPEmergeTests\Routing\Conditions;

use Mockery;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\NegateCondition;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Conditions\NegateCondition
 */
class NegateConditionTest extends TestCase {
	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();
		$condition = Mockery::mock( ConditionInterface::class );

		$condition->shouldReceive( 'isSatisfied' )
			->with( $request )
			->andReturn( true );

		$subject = new NegateCondition( $condition );

		$this->assertFalse( $subject->isSatisfied( $request ) );

		$condition = Mockery::mock( ConditionInterface::class );

		$condition->shouldReceive( 'isSatisfied' )
			->with( $request )
			->andReturn( false );

		$subject = new NegateCondition( $condition );

		$this->assertTrue( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();
		$condition = Mockery::mock( ConditionInterface::class );

		$condition->shouldReceive( 'getArguments' )
			->with( $request )
			->andReturn( ['foo'] );

		$subject = new NegateCondition( $condition );

		$this->assertEquals( ['foo'], $subject->getArguments( $request ) );
	}
}
