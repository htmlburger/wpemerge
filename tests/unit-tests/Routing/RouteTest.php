<?php

namespace WPEmergeTests\Routing;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Helpers\Handler;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Route;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Route
 */
class RouteTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );
		$handler = Mockery::mock( Handler::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$condition->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$subject = (new Route())->attributes( [
			'methods' => ['BAR'],
			'condition' => $condition,
			'handler' => $handler,
		] );
		$this->assertFalse( $subject->isSatisfied( $request ) );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO'],
			'condition' => $condition,
			'handler' => $handler,
		] );
		$this->assertTrue( $subject->isSatisfied( $request ) );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO', 'BAR'],
			'condition' => $condition,
			'handler' => $handler,
		] );
		$this->assertTrue( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_ConditionFalse_False() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );
		$handler = Mockery::mock( Handler::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$condition->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO'],
			'condition' => $condition,
			'handler' => $handler,
		] );
		$this->assertFalse( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments_PassThroughCondition() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );
		$handler = Mockery::mock( Handler::class );
		$expected = ['foo'];

		$condition->shouldReceive( 'getArguments' )
			->with( $request )
			->andReturn( $expected );

		$subject = (new Route())->attributes( [
			'condition' => $condition,
			'handler' => $handler,
		] );

		$this->assertSame( $expected, $subject->getArguments( $request ) );
	}
}
