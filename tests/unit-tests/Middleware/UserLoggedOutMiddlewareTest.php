<?php

namespace WPEmergeTests\Input;

use Mockery;
use WP_UnitTestCase;
use WPEmerge\Middleware\UserLoggedOutMiddleware;
use WPEmerge\Requests\RequestInterface;

/**
 * @coversDefaultClass \WPEmerge\Middleware\UserLoggedOutMiddleware
 */
class UserLoggedOutMiddlewareTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->user_id = 0;
		$this->subject = new UserLoggedOutMiddleware();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		wp_set_current_user( 0 );

		if ( $this->user_id !== 0 ) {
			wp_delete_user( $this->user_id );
		}

		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_LoggedOut_ContinueRequest() {
		$next = function () { return true; };
		$request = Mockery::mock( RequestInterface::class );

		$this->assertTrue( $this->subject->handle( $request, $next ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_LoggedIn_DefaultToHomeUrl() {
		$request = Mockery::mock( RequestInterface::class );
		$next = function () {};
		$url = home_url();

		$this->user_id = $this->factory->user->create();
		wp_set_current_user( $this->user_id );

		$response = $this->subject->handle( $request, $next );

		$this->assertEquals( $url, $response->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_LoggedIn_UseCustomUrl() {
		$request = Mockery::mock( RequestInterface::class );
		$next = function () {};
		$url = home_url( '/foo' );

		$this->user_id = $this->factory->user->create();
		wp_set_current_user( $this->user_id );

		$response = $this->subject->handle( $request, $next, $url );

		$this->assertEquals( $url, $response->getHeaderLine( 'Location' ) );
	}
}
