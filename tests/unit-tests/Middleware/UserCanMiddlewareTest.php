<?php

namespace WPEmergeTests\Input;

use Mockery;
use WP_UnitTestCase;
use WPEmerge\Middleware\UserCanMiddleware;
use WPEmerge\Requests\RequestInterface;

/**
 * @coversDefaultClass \WPEmerge\Middleware\UserCanMiddleware
 */
class UserCanMiddlewareTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->user_ids = [];
		$this->subject = new UserCanMiddleware();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		wp_set_current_user( 0 );

		if ( ! empty( $this->user_ids ) ) {
			foreach ( $this->user_ids as $user_id ) {
				wp_delete_user( $user_id );
			}
		}

		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Can_ContinueRequest() {
		$next = function () { return true; };
		$request = Mockery::mock( RequestInterface::class );

		$this->user_ids['admin'] = $this->factory->user->create( [
			'role' => 'administrator',
		] );
		wp_set_current_user( $this->user_ids['admin'] );

		$this->assertTrue( $this->subject->handle( $request, $next, 'manage_options' ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Cannot_DefaultToHomeUrl() {
		$request = Mockery::mock( RequestInterface::class );
		$next = function () {};
		$url = home_url();

		$this->user_ids['subscriber'] = $this->factory->user->create( [
			'role' => 'subscriber',
		] );
		wp_set_current_user( $this->user_ids['subscriber'] );

		$response = $this->subject->handle( $request, $next, 'manage_options' );

		$this->assertEquals( $url, $response->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Cannot_UseCustomUrl() {
		$request = Mockery::mock( RequestInterface::class );
		$next = function () {};
		$url = home_url( '/foo' );

		$this->user_ids['subscriber'] = $this->factory->user->create( [
			'role' => 'subscriber',
		] );
		wp_set_current_user( $this->user_ids['subscriber'] );

		$response = $this->subject->handle( $request, $next, 'manage_options', 0, $url );

		$this->assertEquals( $url, $response->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_WithObjectId_ContinueRequestOrRedirect() {
		$request = Mockery::mock( RequestInterface::class );
		$next = function () { return true; };
		$url = home_url();

		$this->user_ids['user1'] = $this->factory->user->create( [
			'role' => 'contributor',
		] );

		$this->user_ids['user2'] = $this->factory->user->create( [
			'role' => 'contributor',
		] );

		$post_id = $this->factory->post->create( [
			'post_type' => 'post',
			'post_status' => 'private',
			'post_author' => $this->user_ids['user1'],
		] );

		wp_set_current_user( $this->user_ids['user1'] );
		$this->assertTrue( $this->subject->handle( $request, $next, 'read_post', $post_id ) );

		wp_set_current_user( $this->user_ids['user2'] );
		$response = $this->subject->handle( $request, $next, 'read_post', $post_id );
		$this->assertEquals( $url, $response->getHeaderLine( 'Location' ) );
	}
}
