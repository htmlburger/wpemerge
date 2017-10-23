<?php

use Pimple\Container;
use CarbonFramework\Framework as Subject;

class FrameworkTest extends WP_UnitTestCase {
    /**
     * @covers Subject::debugging
     */
    public function testDebugging() {
        $this->assertEquals( true, Subject::debugging() );
        // can't test for false since WP_DEBUG constant is predefined during testing
    }

    /**
     * @covers Subject::isBooted
     */
    public function testIsBooted() {
        $this->assertEquals( true, Subject::isBooted() );
        // can't test for false since the framework needs to be booted for tests
    }

    /**
     * @covers Subject::getContainer
     */
    public function testGetContainer_ReturnsContainer() {
        $container = Subject::getContainer();
        $this->assertInstanceOf( Container::class, $container );
    }

    /**
     * @covers Subject::getContainer
     */
    public function testGetContainer_CalledMultipleTimes_ReturnsSameContainer() {
        $container1 = Subject::getContainer();
        $container2 = Subject::getContainer();
        $this->assertSame( $container1, $container2 );
    }

    /**
     * @covers Subject::boot
     * @expectedException \Exception
     */
    public function testBoot_CalledMultipleTimes_ThrowsException() {
        Subject::boot();
    }

    /**
     * @covers Subject::facade
     */
    public function testFacade() {
        $expected = 'foobar';

        $container = Subject::getContainer();
        $container['test_service'] = function() {
            return new \CarbonFrameworkTestTools\TestService();
        };
        $alias = 'TestServiceAlias';
        
        Subject::facade( $alias, \CarbonFrameworkTestTools\TestServiceFacade::class );
        $this->assertSame( $expected, call_user_func( [$alias, 'getTest'] ) );
    }

    /**
     * @covers Subject::resolve
     */
    public function testResolve_NonexistantKey_ReturnsNull() {
        $expected = null;
        $container_key = 'nonexistantcontainerkey';

        $this->assertSame( $expected, Subject::resolve( $container_key ) );
    }

    /**
     * @covers Subject::resolve
     */
    public function testResolve_ExistingKey_IsResolved() {
        $expected = 'foobar';
        $container_key = 'test';
        $container_key_nonexistant = 'nonexistantcontainerkey';

        $container = Subject::getContainer();
        $container[ $container_key ] = $expected;
        
        $this->assertSame( $expected, Subject::resolve( $container_key ) );
    }

    /**
     * @covers Subject::instantiate
     */
    public function testInstantiate_UnknownClass_CreatesFreshInstance() {
        $class = \CarbonFrameworkTestTools\TestService::class;
        $instance1 = Subject::instantiate( $class );
        $instance2 = Subject::instantiate( $class );
        
        $this->assertInstanceOf( $class, $instance1 );
        $this->assertInstanceOf( $class, $instance2 );
        $this->assertNotSame( $instance1, $instance2 );
    }

    /**
     * @covers Subject::instantiate
     */
    public function testInstantiate_KnownClass_ResolvesInstanceFromContainer() {
        $expected = rand(1, 999999);
        $class = \CarbonFrameworkTestTools\TestService::class;

        $container = Subject::getContainer();
        $container[ $class ] = function() use ( $expected, $class ) {
            $instance = new $class();
            $instance->setTest( $expected );
            return $instance;
        };
        $instance = Subject::instantiate( $class );

        $this->assertEquals( $expected, $instance->getTest() );
    }
}
