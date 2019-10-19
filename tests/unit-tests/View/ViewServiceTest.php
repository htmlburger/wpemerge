<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\Helpers\Handler;
use WPEmerge\Helpers\HandlerFactory;
use WPEmerge\Support\Facade;
use WPEmerge\View\ViewService;
use WPEmerge\View\ViewInterface;
use WPEmerge\View\ViewEngineInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\ViewService
 */
class ViewServiceTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->engine = Mockery::mock( ViewEngineInterface::class )->shouldIgnoreMissing();
		$this->handler_factory = Mockery::mock( HandlerFactory::class )->shouldIgnoreMissing();
		$this->factory_handler = Mockery::mock( Handler::class );
		$this->subject = Mockery::mock( ViewService::class, [$this->engine, $this->handler_factory] )->makePartial();

		$this->handler_factory->shouldReceive( 'make' )
			->andReturn( $this->factory_handler );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		Facade::clearResolvedInstance( WPEMERGE_VIEW_SERVICE_KEY );

		unset( $this->engine );
		unset( $this->handler_factory );
		unset( $this->factory_handler );
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

		$this->factory_handler->shouldReceive( 'get' )
			->andReturn( $expected );

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
		$view->shouldReceive( 'getContext' )
			->andReturn( [] );
		$view->shouldReceive( 'with' )
			->andReturn( $view );

		$mock = Mockery::mock();
		$mock->shouldReceive( 'foobar' )
			->with( $view )
			->once();

		$composer = function( $view ) use ( $mock ) {
			$mock->foobar( $view );
		};

		$this->factory_handler->shouldReceive( 'execute' )
			->andReturnUsing( $composer );

		$this->subject->addComposer( $view_name, $composer );

		$this->subject->compose( $view );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::compose
	 */
	public function testCompose_GlobalContext() {
		$context = ['foo' => 'bar'];
		$expected = ['global' => $context];
		$view = Mockery::mock( ViewInterface::class )->shouldIgnoreMissing();

		$view->shouldReceive( 'with' )
			->with( $expected )
			->once();

		$this->subject->addGlobals( $context );

		$this->subject->compose( $view );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::compose
	 */
	public function testCompose_ViewComposer() {
		$expected = ['foo' => 'bar'];
		$view = Mockery::mock( ViewInterface::class )->shouldIgnoreMissing();
		$composer = Mockery::mock();

		$view->shouldReceive( 'with' )
			->with( $expected )
			->once();

		$composer->shouldReceive( 'execute' )
			->andReturnUsing( function ( $view ) use ( $expected ) {
				$view->with( $expected );
			} );

		$this->subject->shouldReceive( 'getComposersForView' )
			->andReturn( [$composer] );

		$this->subject->compose( $view );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::compose
	 */
	public function testCompose_LocalContextOverridesViewComposerContext() {
		$composer_context = ['foo' => 1, 'bar' => 1];
		$local_context = ['baz' => 1];
		$view = Mockery::mock( ViewInterface::class )->shouldIgnoreMissing();
		$composer = Mockery::mock();

		$view->shouldReceive( 'getContext' )
			->andReturn( $local_context );

		$composer->shouldReceive( 'execute' )
			->andReturnUsing( function ( $view ) use ( $composer_context ) {
				$view->with( $composer_context );
			} );

		$this->subject->shouldReceive( 'getComposersForView' )
			->andReturn( [$composer] );

		$view->shouldReceive( 'with' )
			->with( $composer_context )
			->once()
			->ordered();

		$view->shouldReceive( 'with' )
			->with( $local_context )
			->once()
			->ordered();

		$this->subject->compose( $view );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::make
	 */
	public function testMake() {
		$view = Mockery::mock( ViewInterface::class );

		$this->engine->shouldReceive( 'make' )
			->with( ['foo'] )
			->andReturn( $view );

		$this->engine->shouldReceive( 'make' )
			->with( ['foo', 'bar'] )
			->andReturn( $view );

		$this->assertSame( $view, $this->subject->make( 'foo' ) );
		$this->assertSame( $view, $this->subject->make( ['foo', 'bar'] ) );
	}
}
