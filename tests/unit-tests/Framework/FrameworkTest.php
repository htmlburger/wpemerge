<?php

namespace WPEmergeTests\Framework;

use Mockery;
use WPEmerge\Framework\Framework;
use WPEmerge\ServiceProviders\ServiceProviderInterface;
use WPEmerge\Support\Facade;
use Pimple\Container;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Framework\Framework
 */
class FrameworkTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->container = new Container();
		$this->subject = new Framework( $this->container );
		$this->facade_application = Facade::getFacadeApplication();
		Facade::setFacadeApplication( $this->container );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		Facade::setFacadeApplication( $this->facade_application );
		unset( $this->subject );
		unset($this->facade_application);
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstruct() {
		$container = new Container();
		$subject = new Framework( $container );
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
	 * @covers ::isBooted
	 * @covers ::boot
	 */
	public function testIsBooted() {
		$this->assertEquals( false, $this->subject->isBooted() );
		$this->subject->boot();
		$this->assertEquals( true, $this->subject->isBooted() );
	}

	/**
	 * @covers ::getContainer
	 */
	public function testGetContainer_ReturnContainer() {
		$container = $this->subject->getContainer();
		$this->assertInstanceOf( Container::class, $container );
	}

	/**
	 * @covers ::verifyBoot
	 * @expectedException \Exception
	 * @expectedExceptionMessage must be booted first
	 */
	public function testVerifyBoot() {
		$this->subject->resolve( 'foobar' );
	}

	/**
	 * @covers ::boot
	 * @expectedException \Exception
	 * @expectedExceptionMessage already booted
	 */
	public function testBoot_CalledMultipleTimes_ThrowException() {
		$this->subject->boot();
		$this->subject->boot();
	}

	/**
	 * @covers ::boot
	 * @covers ::registerServiceProviders
	 * @covers ::bootServiceProviders
	 */
	public function testBoot_RegisterServiceProviders() {
		$container = $this->subject->getContainer();

		$this->subject->boot( [
			'providers' => [
				FrameworkTestServiceProviderMock::class,
			]
		] );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::facade
	 */
	public function testFacade() {
		$expected = 'foobar';

		$container = $this->subject->getContainer();
		$container['test_service'] = function() {
			return new \WPEmergeTestTools\TestService();
		};
		$alias = 'TestServiceAlias';

		$this->subject->facade( $alias, \WPEmergeTestTools\TestServiceFacade::class );
		$this->assertSame( $expected, call_user_func( [$alias, 'getTest'] ) );
	}

	/**
	 * @covers ::resolve
	 * @covers ::verifyBoot
	 */
	public function testResolve_NonexistantKey_ReturnNull() {
		$expected = null;
		$container_key = 'nonexistantcontainerkey';

		$this->subject->boot();
		$this->assertSame( $expected, $this->subject->resolve( $container_key ) );
	}

	/**
	 * @covers ::resolve
	 * @covers ::verifyBoot
	 */
	public function testResolve_ExistingKey_IsResolved() {
		$expected = 'foobar';
		$container_key = 'test';
		$container_key_nonexistant = 'nonexistantcontainerkey';

		$container = $this->subject->getContainer();
		$container[ $container_key ] = $expected;

		$this->subject->boot();
		$this->assertSame( $expected, $this->subject->resolve( $container_key ) );
	}

	/**
	 * @covers ::instantiate
	 * @covers ::verifyBoot
	 */
	public function testInstantiate_UnknownClass_CreateFreshInstance() {
		$class = \WPEmergeTestTools\TestService::class;

		$this->subject->boot();
		$instance1 = $this->subject->instantiate( $class );
		$instance2 = $this->subject->instantiate( $class );

		$this->assertInstanceOf( $class, $instance1 );
		$this->assertInstanceOf( $class, $instance2 );
		$this->assertNotSame( $instance1, $instance2 );
	}

	/**
	 * @covers ::instantiate
	 * @covers ::verifyBoot
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

		$this->subject->boot();
		$instance = $this->subject->instantiate( $class );

		$this->assertEquals( $expected, $instance->getTest() );
	}
}

class FrameworkTestServiceProviderMock implements ServiceProviderInterface {
	public function __construct() {
		$this->mock = Mockery::mock( ServiceProviderInterface::class );
		$this->mock->shouldReceive( 'register' )
			->once();
		$this->mock->shouldReceive( 'boot' )
			->once();
	}

	public function register( $container ) {
		call_user_func_array( [$this->mock, 'register'], func_get_args() );
	}

	public function boot( $container ) {
		call_user_func_array( [$this->mock, 'boot'], func_get_args() );
	}
}
