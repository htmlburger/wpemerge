<?php

namespace WPEmergeTests\Input;

use Mockery;
use WPEmerge\Flash\FlashMiddleware;
use WPEmerge\Requests\RequestInterface;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Flash\FlashMiddleware
 */
class FlashMiddlewareTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->flash = Mockery::mock( \WPEmerge\Flash\Flash::class );
		$this->subject = new FlashMiddleware( $this->flash );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		unset( $this->flash );
		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Disabled_Ignore() {
		$request = Mockery::mock( RequestInterface::class );

		$this->flash->shouldReceive( 'enabled' )
			->andReturn( false );
		$this->flash->shouldNotReceive( 'shift' );
		$this->flash->shouldNotReceive( 'save' );

		$result = $this->subject->handle( $request, function( $request ) { return $request; } );
		$this->assertSame( $request, $result );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Enabled_StoresAll() {
		$request = Mockery::mock( RequestInterface::class );

		$this->flash->shouldReceive( 'enabled' )
			->andReturn( true )
			->ordered();
		$this->flash->shouldReceive( 'shift' )
			->ordered();
		$this->flash->shouldReceive( 'save' )
			->ordered();

		$result = $this->subject->handle( $request, function( $request ) { return $request; } );
		$this->assertSame( $request, $result );
	}
}
