<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\View\ViewService;
use WPEmerge\View\ViewInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\ViewService
 */
class ViewServiceTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->subject = new ViewService();
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
}
