<?php

namespace WPEmergeTests\Input;

use Mockery;
use WPEmerge\Facades\OldInput;
use WPEmerge\Input\OldInputMiddleware;
use WPEmerge\Requests\Request;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Input\OldInputMiddleware
 */
class OldInputMiddlewareTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->oldInputBackup = OldInput::getFacadeRoot();
		$this->oldInput = Mockery::mock();
		OldInput::swap( $this->oldInput );

		$this->subject = new OldInputMiddleware();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		OldInput::swap( $this->oldInputBackup );
		unset( $this->oldInputBackup );
		unset( $this->oldInput );

		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_DisabledPostRequest_Ignore() {
		$request = new Request( [], ['foo' => 'bar'], [], [], ['REQUEST_METHOD' => 'POST'], [] );

		$this->oldInput->shouldReceive( 'enabled' )
			->andReturn( false );
		$this->oldInput->shouldNotReceive( 'set' );

		$result = $this->subject->handle( $request, function( $request ) { return $request; } );
		$this->assertSame( $request, $result );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_EnabledPostRequest_StoresAll() {
		$expected = ['foo' => 'bar'];
		$request = new Request( [], $expected, [], [], ['REQUEST_METHOD' => 'POST'], [] );

		$this->oldInput->shouldReceive( 'enabled' )
			->andReturn( true );
		$this->oldInput->shouldReceive( 'set' )
			->with( $expected );

		$result = $this->subject->handle( $request, function( $request ) { return $request; } );
		$this->assertSame( $request, $result );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_EnabledGetRequest_Ignore() {
		$request = new Request( ['foo' => 'bar'], [], [], [], ['REQUEST_METHOD' => 'GET'], [] );

		$this->oldInput->shouldReceive( 'enabled' )
			->andReturn( true );
		$this->oldInput->shouldNotReceive( 'set' );

		$result = $this->subject->handle( $request, function( $request ) { return $request; } );
		$this->assertSame( $request, $result );
	}
}
