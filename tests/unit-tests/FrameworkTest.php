<?php

use Pimple\Container;
use Obsidian\Framework;

/**
 * @coversDefaultClass \Obsidian\Framework
 */
class FrameworkTest extends WP_UnitTestCase {
    /**
     * @covers ::debugging
     */
    public function testDebugging() {
        $this->assertTrue( Framework::debugging() );
        add_filter( 'obsidian.debug', '__return_false' );
        $this->assertFalse( Framework::debugging() );
    }

    /**
     * @covers ::isBooted
     */
    public function testIsBooted() {
        $this->assertEquals( true, Framework::isBooted() );
        // can't test for false since the framework needs to be booted for tests
    }

    /**
     * @covers ::getContainer
     */
    public function testGetContainer_ReturnsContainer() {
        $container = Framework::getContainer();
        $this->assertInstanceOf( Container::class, $container );
    }

    /**
     * @covers ::getContainer
     */
    public function testGetContainer_CalledMultipleTimes_ReturnsSameContainer() {
        $container1 = Framework::getContainer();
        $container2 = Framework::getContainer();
        $this->assertSame( $container1, $container2 );
    }

    /**
     * @covers ::boot
     * @expectedException \Exception
     * @expectedExceptionMessage already booted
     */
    public function testBoot_CalledMultipleTimes_ThrowsException() {
        Framework::boot();
    }

    /**
     * @covers ::facade
     */
    public function testFacade() {
        $expected = 'foobar';

        $container = Framework::getContainer();
        $container['test_service'] = function() {
            return new \ObsidianTestTools\TestService();
        };
        $alias = 'TestServiceAlias';

        Framework::facade( $alias, \ObsidianTestTools\TestServiceFacade::class );
        $this->assertSame( $expected, call_user_func( [$alias, 'getTest'] ) );
    }

    /**
     * @covers ::resolve
     */
    public function testResolve_NonexistantKey_ReturnsNull() {
        $expected = null;
        $container_key = 'nonexistantcontainerkey';

        $this->assertSame( $expected, Framework::resolve( $container_key ) );
    }

    /**
     * @covers ::resolve
     */
    public function testResolve_ExistingKey_IsResolved() {
        $expected = 'foobar';
        $container_key = 'test';
        $container_key_nonexistant = 'nonexistantcontainerkey';

        $container = Framework::getContainer();
        $container[ $container_key ] = $expected;

        $this->assertSame( $expected, Framework::resolve( $container_key ) );
    }

    /**
     * @covers ::instantiate
     */
    public function testInstantiate_UnknownClass_CreatesFreshInstance() {
        $class = \ObsidianTestTools\TestService::class;
        $instance1 = Framework::instantiate( $class );
        $instance2 = Framework::instantiate( $class );

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

        $container = Framework::getContainer();
        $container[ $class ] = function() use ( $expected, $class ) {
            $instance = new $class();
            $instance->setTest( $expected );
            return $instance;
        };
        $instance = Framework::instantiate( $class );

        $this->assertEquals( $expected, $instance->getTest() );
    }
}
