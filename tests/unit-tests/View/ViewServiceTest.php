<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\View\ViewService;
use WPEmerge\View\ViewEngineInterface;
use WPEmerge\View\ViewInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\ViewService
 */
class ViewServiceTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new ViewService( Mockery::mock( ViewEngineInterface::class ) );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::addGlobal
	 * @covers ::getGlobals
	 */
	public function testaddGlobal() {
		$expected = ['foo' => 'bar'];

		$this->subject->addGlobal( 'foo', 'bar' );

		$this->assertEquals( $expected, $this->subject->getGlobals() );
	}

	/**
	 * @covers ::addGlobals
	 * @covers ::getGlobals
	 */
	public function testaddGlobals() {
		$expected = ['foo' => 'bar'];

		$this->subject->addGlobals( $expected );

		$this->assertEquals( $expected, $this->subject->getGlobals() );
	}

	/**
	 * @covers ::addComposer
	 * @covers ::getComposersForView
	 */
	public function testAddComposer() {
		$expected = function () { return []; };
		$view = 'foo';

		$this->subject->addComposer( $view, $expected );

		$this->assertSame( $expected, $this->subject->getComposersForView( $view )[0]->get() );
	}

	/**
	 * @covers ::compose
	 */
	public function testCompose() {
		$view_name = 'foo';

		$view = Mockery::mock( ViewInterface::class );
		$view->shouldReceive( 'getName' )
			->andReturn( $view_name );

		$mock = Mockery::mock();
		$mock->shouldReceive( 'foobar' )
			->with( $view );

		$composer = function( $view ) use ( $mock ) {
			$mock->foobar( $view );
		};

		$this->subject->addComposer( $view_name, $composer );

		$this->subject->compose( $view );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::make
	 */
	public function testMake() {
		$view_engine = Mockery::mock( ViewEngineInterface::class );
		$view = Mockery::mock( ViewInterface::class );
		$subject = new ViewService( $view_engine );

		$view_engine->shouldReceive( 'make' )
			->with( ['foo'], ['foo'] )
			->andReturn( $view );

		$view_engine->shouldReceive( 'make' )
			->with( ['foo', 'bar'], ['foobar'] )
			->andReturn( $view );

		$this->assertSame( $view, $subject->make( 'foo', ['foo'] ) );
		$this->assertSame( $view, $subject->make( ['foo', 'bar'], ['foobar'] ) );
	}

	/**
	 * @covers ::toString
	 */
	public function testToString() {
		$view_engine = Mockery::mock( ViewEngineInterface::class );
		$view = Mockery::mock( ViewInterface::class );
		$subject = new ViewService( $view_engine );

		$view_engine->shouldReceive( 'make' )
			->with( ['foo'], ['foo'] )
			->andReturn( $view );

		$view_engine->shouldReceive( 'make' )
			->with( ['foo', 'bar'], ['foobar'] )
			->andReturn( $view );

		$view->shouldReceive( 'toString' )
			->andReturn( 'foo', 'foobar' );

		$this->assertEquals( 'foo', $subject->toString( 'foo', ['foo'] ) );
		$this->assertEquals( 'foobar', $subject->toString( ['foo', 'bar'], ['foobar'] ) );
	}
}
