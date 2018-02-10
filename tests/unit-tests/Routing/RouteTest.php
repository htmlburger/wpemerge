<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge;
use WPEmerge\Requests\Request;
use WPEmerge\Routing\RouteHandler;
use WPEmerge\Routing\Route;
use WPEmerge\Routing\Conditions\ConditionInterface;
use WPEmerge\Routing\Conditions\Custom;
use stdClass;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Route
 */
class RouteTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		// $this->subject = new Route();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		// unset( $this->subject );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getMethods
	 * @covers ::getTarget
	 * @covers ::getHandler
	 */
	public function testConstruct_ConditionInterface() {
		$expected_methods = ['FOO'];
		$expected_target = Mockery::mock( ConditionInterface::class );
		$handler = function() {};
		$expected_handler = new RouteHandler( $handler );

		$subject = new Route( $expected_methods, $expected_target, $handler );
		$this->assertEquals( $expected_methods, $subject->getMethods() );
		$this->assertSame( $expected_target, $subject->getTarget() );
		$this->assertEquals( $expected_handler, $subject->getHandler() );
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstruct_Closure() {
		$expected = function() {};

		$subject = new Route( [], $expected, function() {} );
		$this->assertEquals( $expected, $subject->getTarget()->getCallable() );
	}

	/**
	 * @covers ::__construct
	 * @expectedException \Exception
	 * @expectedExceptionMessage Route target is not a valid
	 */
	public function testConstruct_Invalid() {
		$subject = new Route( [], new stdClass(), function() {} );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( Request::class );
		$target = Mockery::mock( ConditionInterface::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$target->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$subject1 = new Route( ['BAR'], $target, function() {} );
		$this->assertFalse( $subject1->isSatisfied( $request ) );

		$subject2 = new Route( ['FOO'], $target, function() {} );
		$this->assertTrue( $subject2->isSatisfied( $request ) );

		$subject3 = new Route( ['FOO', 'BAR'], $target, function() {} );
		$this->assertTrue( $subject3->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_ConditionFalse_False() {
		$request = Mockery::mock( Request::class );
		$target = Mockery::mock( ConditionInterface::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$target->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$subject = new Route( ['FOO'], $target, function() {} );
		$this->assertFalse( $subject->isSatisfied( $request ) );
	}
}
