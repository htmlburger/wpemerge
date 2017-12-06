<?php

namespace ObsidianTests\Framework;

use Obsidian\Framework\Framework;
use Obsidian\Support\Facade;
use Pimple\Container;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \Obsidian\Framework\Framework
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

        Facade::setFacadeApplication( $this->facade_application );
        unset( $this->subject );
        unset($this->facade_application);
    }

    /**
     * @covers ::debugging
     */
    public function testDebugging() {
        $this->assertTrue( $this->subject->debugging() );
        add_filter( 'obsidian.debug', '__return_false' );
        $this->assertFalse( $this->subject->debugging() );
    }

    /**
     * @covers ::isBooted
     */
    public function testIsBooted() {
        $this->assertEquals( false, $this->subject->isBooted() );
        $this->subject->boot();
        $this->assertEquals( true, $this->subject->isBooted() );
    }

    /**
     * @covers ::getContainer
     */
    public function testGetContainer_ReturnsContainer() {
        $container = $this->subject->getContainer();
        $this->assertInstanceOf( Container::class, $container );
    }

    /**
     * @covers ::boot
     * @expectedException \Exception
     * @expectedExceptionMessage already booted
     */
    public function testBoot_CalledMultipleTimes_ThrowsException() {
        $this->subject->boot();
        $this->subject->boot();
    }

    /**
     * @covers ::facade
     */
    public function testFacade() {
        $expected = 'foobar';

        $container = $this->subject->getContainer();
        $container['test_service'] = function() {
            return new \ObsidianTestTools\TestService();
        };
        $alias = 'TestServiceAlias';

        $this->subject->facade( $alias, \ObsidianTestTools\TestServiceFacade::class );
        $this->assertSame( $expected, call_user_func( [$alias, 'getTest'] ) );
    }

    /**
     * @covers ::resolve
     */
    public function testResolve_NonexistantKey_ReturnsNull() {
        $expected = null;
        $container_key = 'nonexistantcontainerkey';

        $this->subject->boot();
        $this->assertSame( $expected, $this->subject->resolve( $container_key ) );
    }

    /**
     * @covers ::resolve
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
     */
    public function testInstantiate_UnknownClass_CreatesFreshInstance() {
        $class = \ObsidianTestTools\TestService::class;

        $this->subject->boot();
        $instance1 = $this->subject->instantiate( $class );
        $instance2 = $this->subject->instantiate( $class );

        $this->assertInstanceOf( $class, $instance1 );
        $this->assertInstanceOf( $class, $instance2 );
        $this->assertNotSame( $instance1, $instance2 );
    }

    /**
     * @covers ::instantiate
     */
    public function testInstantiate_KnownClass_ResolvesInstanceFromContainer() {
        $expected = rand(1, 999999);
        $class = \ObsidianTestTools\TestService::class;

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
