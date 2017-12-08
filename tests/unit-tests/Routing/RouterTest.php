<?php

namespace WPEmergeTests\Routing;

use Mockery;
use WPEmerge;
use WPEmerge\Routing\Router;
use WPEmerge\Routing\RouteInterface;
use WPEmerge\Middleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Router
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

        $container_key = WPEMERGE_ROUTING_GLOBAL_MIDDLEWARE_KEY;
        $container = WPEmerge::getContainer();

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

        $route1->shouldReceive( 'isSatisfied' )
            ->andReturn( false )
            ->once();

        $route2->shouldReceive( 'isSatisfied' )
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

        $route1->shouldReceive( 'isSatisfied' )
            ->andReturn( true )
            ->once();

        $route1->shouldReceive( 'handle' )
            ->andReturn( $response )
            ->once();

        $route2->shouldReceive( 'isSatisfied' )
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

        $route->shouldReceive( 'isSatisfied' )
            ->andReturn( true )
            ->once();

        $route->shouldReceive( 'handle' )
            ->andReturn( new stdClass() )
            ->once();

        $this->subject->addRoute( $route );

        add_filter( 'wpemerge.debug', '__return_false' );

        $this->subject->execute( '' );

        $response = apply_filters( 'wpemerge.response', null );
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

        $route->shouldReceive( 'isSatisfied' )
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

        $route->shouldReceive( 'isSatisfied' )
            ->andReturn( true )
            ->once();

        $route->shouldReceive( 'handle' )
            ->andReturn( $response )
            ->once();

        $this->subject->addRoute( $route );

        $this->subject->execute( '' );

        $filter_response = apply_filters( 'wpemerge.response', null );
        $this->assertSame( $response, $filter_response );
    }

    /**
     * @covers ::execute
     * @covers ::handle
     */
    public function testExecute_Response_ReturnsBuiltInView() {
        $expected = WPEMERGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php';
        $route = Mockery::mock( RouteInterface::class )->shouldIgnoreMissing();
        $response = Mockery::mock( ResponseInterface::class )->shouldIgnoreMissing();

        $route->shouldReceive( 'isSatisfied' )
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
