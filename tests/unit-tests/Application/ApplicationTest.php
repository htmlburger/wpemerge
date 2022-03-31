<?php

namespace WPEmergeTests\Application;

use Exception;
use Mockery;
use Pimple\Container;
use WPEmerge\Application\Application;
use WPEmerge\ServiceProviders\ServiceProviderInterface;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\Application
 */
class ApplicationTest extends TestCase {
	public $container;

	public $subject;

	public function set_up() {
		$this->container = new Container();
		$this->subject = new Application( $this->container, false );
		$this->container[ WPEMERGE_APPLICATION_KEY ] = $this->subject;
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->container );
		unset( $this->subject );
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstruct() {
		$container = new Container();
		$subject = new Application( $container );
		$this->assertSame( $container, $subject->container() );
	}

	/**
	 * @covers ::isBootstrapped
	 * @covers ::bootstrap
	 */
	public function testIsBootstrapped() {
		$this->assertEquals( false, $this->subject->isBootstrapped() );
		$this->subject->bootstrap( [], false );
		$this->assertEquals( true, $this->subject->isBootstrapped() );
	}

	/**
	 * @covers ::bootstrap
	 */
	public function testBootstrap_CalledMultipleTimes_ThrowException() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'already bootstrapped' );
		$this->subject->bootstrap( [], false );
		$this->subject->bootstrap( [], false );
	}

	/**
	 * @covers ::bootstrap
	 * @covers ::registerServiceProviders
	 * @covers ::bootstrapServiceProviders
	 */
	public function testBootstrap_RegisterServiceProviders() {
		$this->subject->bootstrap( [
			'providers' => [
				ApplicationTestServiceProviderMock::class,
			]
		], false );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::bootstrap
	 */
	public function testBootstrap_RunKernel() {
		$this->subject->bootstrap( [
			'providers' => [
				ApplicationTestKernelServiceProviderMock::class,
			],
		], true );

		$this->assertTrue( true );
	}

	/**
	 * @covers ::resolve
	 */
	public function testResolve_NonexistentKey_ReturnNull() {
		$expected = null;
		$container_key = 'nonexistentcontainerkey';

		$this->subject->bootstrap( [], false );
		$this->assertSame( $expected, $this->subject->resolve( $container_key ) );
	}

	/**
	 * @covers ::resolve
	 */
	public function testResolve_ExistingKey_IsResolved() {
		$expected = 'foobar';
		$container_key = 'test';

		$container = $this->subject->container();
		$container[ $container_key ] = $expected;

		$this->subject->bootstrap( [], false );
		$this->assertSame( $expected, $this->subject->resolve( $container_key ) );
	}
}

class ApplicationTestServiceProviderMock implements ServiceProviderInterface {
	public function __construct() {
		$this->mock = Mockery::mock( ServiceProviderInterface::class );
		$this->mock->shouldReceive( 'register' )
			->once();
		$this->mock->shouldReceive( 'bootstrap' )
			->once();
	}

	public function register( $container ) {
		call_user_func_array( [$this->mock, 'register'], func_get_args() );
	}

	public function bootstrap( $container ) {
		call_user_func_array( [$this->mock, 'bootstrap'], func_get_args() );
	}
}

class ApplicationTestKernelServiceProviderMock implements ServiceProviderInterface {
	public function register( $container ) {
		$mock = Mockery::mock();

		$mock->shouldReceive( 'bootstrap' )
			->once();

		$container[ WPEMERGE_WORDPRESS_HTTP_KERNEL_KEY ] = $mock;
	}

	public function bootstrap( $container ) {
		// Do nothing.
	}
}
