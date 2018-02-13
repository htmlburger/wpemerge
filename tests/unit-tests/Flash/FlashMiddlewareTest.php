<?php

namespace WPEmergeTests\Input;

use Mockery;
use WPEmerge\Facades\Flash;
use WPEmerge\Flash\FlashMiddleware;
use WPEmerge\Requests\Request;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Flash\FlashMiddleware
 */
class FlashMiddlewareTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->flashBackup = Flash::getFacadeRoot();
		$this->flash = Mockery::mock();
		Flash::swap( $this->flash );

		$this->subject = new FlashMiddleware();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		Flash::swap( $this->flashBackup );
		unset( $this->flashBackup );
		unset( $this->flash );

		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Disabled_Ignore() {
		$request = new Request( [], ['foo' => 'bar'], [], [], ['REQUEST_METHOD' => 'POST'], [] );

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
		$expected = ['foo' => 'bar'];
		$request = new Request( [], $expected, [], [], ['REQUEST_METHOD' => 'POST'], [] );

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
