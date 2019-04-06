<?php

namespace WPEmergeTests\Routing;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\UrlCondition;
use WPEmerge\Routing\Pipeline;
use WPEmerge\Routing\Route;
use WPEmerge\Routing\Conditions\ConditionInterface;
use stdClass;
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
	 * @covers ::where
	 */
	public function testWhere_UrlCondition_Appended() {
		$condition = Mockery::mock( UrlCondition::class );
		$subject = new Route( [], $condition, function() {} );
		$expected1 = ['foo' => '/^foo$/i'];
		$expected2 = ['foo' => '/^foo$/i', 'bar' => '/^bar$/i'];

		$condition->shouldReceive( 'getUrlWhere' )
			->andReturn( [] )
			->once();

		$condition->shouldReceive( 'setUrlWhere' )
			->with( $expected1 )
			->once();

		$subject->where( 'foo', $expected1['foo'] );

		$condition->shouldReceive( 'getUrlWhere' )
			->andReturn( $expected1 )
			->once();

		$condition->shouldReceive( 'setUrlWhere' )
			->with( $expected2 )
			->once();

		$subject->where( 'bar', $expected2['bar'] );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::where
	 * @expectedException \WPEmerge\Exceptions\Exception
	 * @expectedExceptionMessage Only routes with URL conditions
	 */
	public function testWhere_NonUrlCondition_Exception() {
		$condition = Mockery::mock( ConditionInterface::class );
		$subject = new Route( [], $condition, function() {} );

		$subject->where( 'foo', '/^foo$/i' );
	}

	/**
	 * @covers ::getQueryFilter
	 * @covers ::setQueryFilter
	 */
	public function testGetQueryFilter() {
		$condition = Mockery::mock( ConditionInterface::class );
		$subject = new Route( [], $condition, function() {} );
		$filter = function() {};

		$subject->setQueryFilter( $filter );
		$this->assertSame( $filter, $subject->getQueryFilter() );
	}

	/**
	 * @covers ::addQueryFilter
	 * @covers ::removeQueryFilter
	 */
	public function testAddQueryFilter() {
		$condition = Mockery::mock( ConditionInterface::class );
		$subject = new Route( [], $condition, function() {} );

		$subject->addQueryFilter();
		$this->assertEquals( 1000, has_action( 'request', [$subject, 'applyQueryFilter'] ) );

		$subject->removeQueryFilter();
		$this->assertFalse( has_action( 'request', [$subject, 'applyQueryFilter'] ) );
	}

	/**
	 * @covers ::applyQueryFilter
	 */
	public function testApplyQueryFilter_NoFilter_NoChange() {
		$condition = Mockery::mock( ConditionInterface::class );
		$subject = new Route( [], $condition, function() {} );

		$this->assertEquals( [], $subject->applyQueryFilter( [] ) );
	}

	/**
	 * @covers ::applyQueryFilter
	 * @expectedException \WPEmerge\Exceptions\Exception
	 * @expectedExceptionMessage Routes with queries can only use URL conditions
	 */
	public function testApplyQueryFilter_NonUrlCondition_Exception() {
		$condition = Mockery::mock( ConditionInterface::class );
		$subject = new Route( [], $condition, function() {} );
		$subject->query( function() {} );

		$subject->applyQueryFilter( [] );
	}

	/**
	 * @covers ::applyQueryFilter
	 */
	public function testApplyQueryFilter_UnsatisfiedUrlCondition_NoChange() {
		$condition = Mockery::mock( UrlCondition::class );
		$subject = new Route( [], $condition, function() {} );
		$subject->query( function() {} );

		$condition->shouldReceive( 'isSatisfied' )
			  ->andReturn( false );

		$this->assertEquals( [], $subject->applyQueryFilter( [] ) );
	}

	/**
	 * @covers ::applyQueryFilter
	 */
	public function testApplyQueryFilter_SatisfiedUrlCondition_ArrayFiltered() {
		$arguments = ['arg1', 'arg2'];
		$condition = Mockery::mock( UrlCondition::class );
		$subject = new Route( [], $condition, function() {} );
		$subject->query( function( $query_vars, $arg1, $arg2 ) {
			return array_merge( $query_vars, [$arg1, $arg2] );
		} );

		$condition->shouldReceive( 'isSatisfied' )
			  ->andReturn( true );

		$condition->shouldReceive( 'getArguments' )
				  ->andReturn( $arguments );

		$this->assertEquals( ['arg0', 'arg1', 'arg2'], $subject->applyQueryFilter( ['arg0'] ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$condition->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$subject1 = new Route( ['BAR'], $condition, function() {} );
		$this->assertFalse( $subject1->isSatisfied( $request ) );

		$subject2 = new Route( ['FOO'], $condition, function() {} );
		$this->assertTrue( $subject2->isSatisfied( $request ) );

		$subject3 = new Route( ['FOO', 'BAR'], $condition, function() {} );
		$this->assertTrue( $subject3->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_ConditionFalse_False() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$condition->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$subject = new Route( ['FOO'], $condition, function() {} );
		$this->assertFalse( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments_PassThroughCondition() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );
		$expected = ['foo'];

		$condition->shouldReceive( 'getArguments' )
				  ->with( $request )
				  ->andReturn( $expected );

		$subject = new Route( [], $condition, function() {} );
		$this->assertSame( $expected, $subject->getArguments( $request ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle() {
		$request = Mockery::mock( RequestInterface::class );
		$view = 'foobar.php';
		$condition = Mockery::mock( ConditionInterface::class );
		$expected = Mockery::mock( ResponseInterface::class );
		$subject = new Route( [], $condition, function( $a, $b, $c, $d ) use ( $request, $view, $expected ) {
			$this->assertEquals( [$request, $view, 'foo', 'bar'], [$a, $b, $c, $d] );
			return $expected;
		} );

		$condition->shouldReceive( 'getArguments' )
			->with( $request )
			->andReturn( ['foo', 'bar'] );

		$this->assertSame( $expected, $subject->handle( $request, $view ) );
	}
}
