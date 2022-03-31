<?php

namespace WPEmergeTests\Input;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use WPEmerge\Middleware\UserLoggedInMiddleware;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Responses\ResponseService;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Middleware\UserLoggedInMiddleware
 */
class UserLoggedInMiddlewareTest extends TestCase {
	public function set_up() {
		$this->user_id = 0;
		$this->response_service = Mockery::mock( ResponseService::class );
		$this->response = Mockery::mock( ResponseInterface::class );
		$this->subject = new UserLoggedInMiddleware( $this->response_service );
	}

	public function tear_down() {
		Mockery::close();

		wp_set_current_user( 0 );

		if ( $this->user_id !== 0 ) {
			wp_delete_user( $this->user_id );
		}

		unset( $this->response_service );
		unset( $this->response );
		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_LoggedIn_ContinueRequest() {
		$next = function () { return true; };
		$request = Mockery::mock( RequestInterface::class );

		$this->user_id = $this->factory->user->create();
		wp_set_current_user( $this->user_id );

		$this->assertTrue( $this->subject->handle( $request, $next ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_LoggedOut_DefaultToLoginUrl() {
		$request = Mockery::mock( RequestInterface::class );
		$next = function () {};
		$url = wp_login_url( 'foo' );

		$request->shouldReceive( 'getUrl' )
			->andReturn( 'foo' );

		$this->response_service->shouldReceive( 'redirect' )
			->andReturn( $this->response );

		$this->response->shouldReceive( 'to' )
			->with( $url )
			->andReturn( $this->response )
			->once();

		$this->assertSame( $this->response, $this->subject->handle( $request, $next ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_LoggedOut_UseCustomUrl() {
		$request = Mockery::mock( RequestInterface::class );
		$next = function () {};
		$url = wp_login_url( 'foo' );

		$this->response_service->shouldReceive( 'redirect' )
			->andReturn( $this->response );

		$this->response->shouldReceive( 'to' )
			->with( $url )
			->andReturn( $this->response )
			->once();

		$this->assertSame( $this->response, $this->subject->handle( $request, $next, $url ) );
	}
}
