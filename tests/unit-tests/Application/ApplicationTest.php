<?php

namespace WPEmergeTests\Application;

use Mockery;
use WPEmerge\Application\Application;
use WPEmerge\ServiceProviders\ServiceProviderInterface;
use WPEmerge\Support\Facade;
use Pimple\Container;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\Application
 */
class ApplicationTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->container = new Container();
		$this->subject = new Application( $this->container, false );
		$this->container[ WPEMERGE_APPLICATION_KEY ] = $this->subject;
		$this->facade_application = Facade::getFacadeApplication();
		Facade::setFacadeApplication( $this->container );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		Facade::setFacadeApplication( $this->facade_application );
		unset( $this->container );
		unset( $this->subject );
		unset( $this->facade_application );
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstruct() {
		$container = new Container();
		$subject = new Application( $container );
		$this->assertSame( $container, $subject->getContainer() );
	}

	/**
	 * @covers ::debugging
	 */
	public function testDebugging() {
		$this->assertTrue( $this->subject->debugging() );
		add_filter( 'wpemerge.debug', '__return_false' );
		$this->assertFalse( $this->subject->debugging() );
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
	 * @covers ::getContainer
	 */
	public function testGetContainer_ReturnContainer() {
		$container = $this->subject->getContainer();
		$this->assertInstanceOf( Container::class, $container );
	}

	/**
	 * @covers ::verifyBootstrap
	 * @expectedException \Exception
	 * @expectedExceptionMessage must be bootstrapped first
	 */
	public function testVerifyBootstrap() {
		$this->subject->resolve( 'foobar' );
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
	 * @covers ::alias
	 */
	public function testAlias() {
		$expected = 'foobar';

		$container = $this->subject->getContainer();
		$container['test_service'] = function() {
			return new \WPEmergeTestTools\TestService();
		};
		$alias = 'TestServiceAlias';

		$this->subject->alias( $alias, \WPEmergeTestTools\TestServiceFacade::class );
		$this->assertSame( $expected, call_user_func( [$alias, 'getTest'] ) );
	}

	/**
	 * @covers ::resolve
	 * @covers ::verifyBootstrap
	 */
	public function testResolve_NonexistantKey_ReturnNull() {
		$expected = null;
		$container_key = 'nonexistantcontainerkey';

		$this->subject->bootstrap( [], false );
		$this->assertSame( $expected, $this->subject->resolve( $container_key ) );
	}

	/**
	 * @covers ::resolve
	 * @covers ::verifyBootstrap
	 */
	public function testResolve_ExistingKey_IsResolved() {
		$expected = 'foobar';
		$container_key = 'test';

		$container = $this->subject->getContainer();
		$container[ $container_key ] = $expected;

		$this->subject->bootstrap( [], false );
		$this->assertSame( $expected, $this->subject->resolve( $container_key ) );
	}

	/**
	 * @covers ::instantiate
	 * @covers ::verifyBootstrap
	 */
	public function testInstantiate_UnknownClass_CreateFreshInstance() {
		$class = \WPEmergeTestTools\TestService::class;

		$this->subject->bootstrap( [], false );
		$instance1 = $this->subject->instantiate( $class );
		$instance2 = $this->subject->instantiate( $class );

		$this->assertInstanceOf( $class, $instance1 );
		$this->assertInstanceOf( $class, $instance2 );
		$this->assertNotSame( $instance1, $instance2 );
	}

	/**
	 * @covers ::instantiate
	 * @expectedException \WPEmerge\Application\ClassNotFoundException
	 * @expectedExceptionMessage Class not found
	 */
	public function testInstantiate_UnknownNonexistantClass_Exception() {
		$class = \WPEmergeTestTools\NonExistantClass::class;

		$this->subject->bootstrap( [], false );
		$this->subject->instantiate( $class );
	}

	/**
	 * @covers ::instantiate
	 * @covers ::verifyBootstrap
	 */
	public function testInstantiate_KnownClass_ResolveInstanceFromContainer() {
		$expected = rand(1, 999999);
		$class = \WPEmergeTestTools\TestService::class;

		$container = $this->subject->getContainer();
		$container[ $class ] = function() use ( $expected, $class ) {
			$instance = new $class();
			$instance->setTest( $expected );
			return $instance;
		};

		$this->subject->bootstrap( [], false );
		$instance = $this->subject->instantiate( $class );

		$this->assertEquals( $expected, $instance->getTest() );
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
