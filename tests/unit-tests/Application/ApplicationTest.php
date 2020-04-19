<?php

namespace WPEmergeTests\Application;

use Mockery;
use WPEmerge\Application\Application;
use WPEmerge\ServiceProviders\ServiceProviderInterface;
use Pimple\Container;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\Application
 */
class ApplicationTest extends WP_UnitTestCase {
	public $container;

	public $subject;

	public function setUp() {
		parent::setUp();

		$this->container = new Container();
		$this->subject = new Application( $this->container, false );
		$this->container[ WPEMERGE_APPLICATION_KEY ] = $this->subject;
	}

	public function tearDown() {
		parent::tearDown();
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
	 * @expectedException \Exception
	 * @expectedExceptionMessage already bootstrapped
	 */
	public function testBootstrap_CalledMultipleTimes_ThrowException() {
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
	public function testResolve_NonexistantKey_ReturnNull() {
		$expected = null;
		$container_key = 'nonexistantcontainerkey';

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

	/**
	 * @covers ::alias
	 */
	public function testAlias_String_ResolveFromContainer() {
		$alias = 'test';
		$service_key = 'test_service';

		$container = $this->subject->container();
		$container[ $service_key ] = function() {
			return new \WPEmergeTestTools\TestService();
		};

		$this->subject->bootstrap( [], false );
		$this->subject->alias( $alias, $service_key );

		$this->assertSame( $container[ $service_key ], $this->subject->{$alias}() );
	}

	/**
	 * @covers ::alias
	 */
	public function testAlias_Closure_CallClosure() {
		$expected = 'foo';
		$alias = 'test';
		$closure = function () use ( $expected ) {
			return $expected;
		};

		$this->subject->bootstrap( [], false );
		$this->subject->alias( $alias, $closure );

		$this->assertEquals( $expected, $this->subject->{$alias}() );
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
