<?php

use Obsidian\Framework;
use Obsidian\Routing\Router;
use Obsidian\Routing\RouteInterface;
use Obsidian\Middleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @coversDefaultClass \Obsidian\Routing\Router
 */
class RouterTest extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();

        $this->subject = new Router();
    }

    public function tearDown() {
        parent::tearDown();
        Mockery::close();

        unset( $this->subject );
    }

    /**
     * @covers ::getCurrentRoute
     * @covers ::setCurrentRoute
     */
    public function testSetCurrentRoute() {
        $expected = Mockery::mock( RouteInterface::class );

        $this->subject->setCurrentRoute( $expected );
        $this->assertSame( $expected, $this->subject->getCurrentRoute() );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_GlobalMiddleware_AddToRoutes() {
        $route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
        $middleware = Mockery::mock( MiddlewareInterface::class );
        $middleware_array = [$middleware];

        $container_key = 'framework.routing.global_middleware';
        $container = Framework::getContainer();

        $route->shouldReceive( 'addMiddleware' )
            ->with( $middleware_array )
            ->once();

        $this->subject->addRoute( $route );

        $backup = $container[ $container_key ];
        $container[ $container_key ] = $middleware_array;
        $this->subject->execute( '' );
        $container[ $container_key ] = $backup;

        $this->assertTrue( true );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_Routes_CheckIfRoutesAreSatisfied() {
        $route1 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
        $route2 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();

        $route1->shouldReceive( 'satisfied' )
            ->andReturn( false )
            ->once();

        $route2->shouldReceive( 'satisfied' )
            ->andReturn( false )
            ->once();

        $this->subject->addRoute( $route1 );
        $this->subject->addRoute( $route2 );

        $this->subject->execute( '' );

        $this->assertTrue( true );
    }

    /**
     * @covers ::execute
     */
    public function testExecute_SatisfiedRoute_StopCheckingCallHandleSetCurrent() {
        $route1 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
        $route2 = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
        $response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();

        $route1->shouldReceive( 'satisfied' )
            ->andReturn( true )
            ->once();

        $route1->shouldReceive( 'handle' )
            ->andReturn( $response )
            ->once();

        $route2->shouldReceive( 'satisfied' )
            ->never();

        $this->subject->addRoute( $route1 );
        $this->subject->addRoute( $route2 );

        $this->subject->execute( '' );

        $this->assertSame( $route1, $this->subject->getCurrentRoute() );
    }

    /**
     * @covers ::execute
     * @covers ::handle
     */
    public function testExecute_InvalidResponse_ReturnErrorResponse() {
        $route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();

        $route->shouldReceive( 'satisfied' )
            ->andReturn( true )
            ->once();

        $route->shouldReceive( 'handle' )
            ->andReturn( new stdClass() )
            ->once();

        $this->subject->addRoute( $route );

        add_filter( 'obsidian.debug', '__return_false' );

        $this->subject->execute( '' );

        $response = apply_filters( 'obsidian.response', null );
        $this->assertEquals( 500, $response->getStatusCode() );
    }

    /**
     * @covers ::execute
     * @covers ::handle
     * @expectedException \Exception
     * @expectedExceptionMessage Response returned by controller is not valid
     */
    public function testExecute_DebugInvalidResponse_ThrowsException() {
        $route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();

        $route->shouldReceive( 'satisfied' )
            ->andReturn( true )
            ->once();

        $route->shouldReceive( 'handle' )
            ->andReturn( new stdClass() )
            ->once();

        $this->subject->addRoute( $route );

        $this->subject->execute( '' );
    }

    /**
     * @covers ::execute
     * @covers ::handle
     */
    public function testExecute_Response_AddsFilter() {
        $route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
        $response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();

        $route->shouldReceive( 'satisfied' )
            ->andReturn( true )
            ->once();

        $route->shouldReceive( 'handle' )
            ->andReturn( $response )
            ->once();

        $this->subject->addRoute( $route );

        $this->subject->execute( '' );

        $filter_response = apply_filters( 'obsidian.response', null );
        $this->assertSame( $response, $filter_response );
    }

    /**
     * @covers ::execute
     * @covers ::handle
     */
    public function testExecute_Response_ReturnsBuiltInTemplate() {
        $expected = OBSIDIAN_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'template.php';
        $route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
        $response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();

        $route->shouldReceive( 'satisfied' )
            ->andReturn( true )
            ->once();

        $route->shouldReceive( 'handle' )
            ->andReturn( $response )
            ->once();

        $this->subject->addRoute( $route );

        $this->assertSame( $expected, $this->subject->execute( '' ) );
    }

    /**
     * @covers ::handleAll
     */
    public function testHandleAll() {
        $expected = $this->subject->any( '*' );

        $result = $this->subject->handleAll();

        $this->assertEquals( $expected, $result );
    }
}
