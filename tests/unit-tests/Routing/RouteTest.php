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

		$subject1 = new Route( ['BAR'], $condition, $handler );
		$this->assertFalse( $subject1->isSatisfied( $request ) );

		$subject2 = new Route( ['FOO'], $condition, $handler );
		$this->assertTrue( $subject2->isSatisfied( $request ) );

		$subject3 = new Route( ['FOO', 'BAR'], $condition, $handler );
		$this->assertTrue( $subject3->isSatisfied( $request ) );
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

		$subject = new Route( ['FOO'], $condition, $handler );
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

		$subject = new Route( [], $condition, $handler );
		$this->assertSame( $expected, $subject->getArguments( $request ) );
	}

	/**
	 * @covers ::decorate
	 */
	public function testDecorate_Middleware() {
		$condition = Mockery::mock( ConditionInterface::class );
		$handler = Mockery::mock( Handler::class );
		$expected = ['foo'];
		$subject = new Route( [], $condition, $handler );

		$subject->decorate( [
			'middleware' => $expected,
		] );

		$this->assertEquals( $expected, $subject->getMiddleware() );
	}

	/**
	 * @covers ::decorate
	 */
	public function testDecorate_Query() {
		$condition = Mockery::mock( ConditionInterface::class );
		$handler = Mockery::mock( Handler::class );
		$subject = new Route( [], $condition, $handler );

		$subject->decorate( [] );
		$this->assertNull( $subject->getQueryFilter() );

		$subject->decorate( [
			'query' => null,
		] );
		$this->assertNull( $subject->getQueryFilter() );

		$query = function () {};
		$subject->decorate( [
			'query' => $query,
		] );
		$this->assertSame( $query, $subject->getQueryFilter() );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle() {
		$request = Mockery::mock( RequestInterface::class );
		$view = 'foobar.php';
		$condition = Mockery::mock( ConditionInterface::class );
		$handler = Mockery::mock( Handler::class );
		$expected = Mockery::mock( ResponseInterface::class );
		$subject = new Route( [], $condition, $handler );

		$handler->shouldReceive( 'execute' )
			->andReturnUsing( function( $a, $b, $c, $d ) use ( $request, $view, $expected ) {
				$this->assertEquals( [$request, $view, 'foo', 'bar'], [$a, $b, $c, $d] );
				return $expected;
			} );

		$this->assertSame( $expected, $subject->handle( $request, [$request, $view, 'foo', 'bar'] ) );
	}
}
