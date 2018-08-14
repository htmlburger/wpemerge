<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\Facades\Framework;
use WPEmerge\View\NameProxyViewEngine;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\NameProxyViewEngine
 */
class NameProxyViewEngineTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->container = Framework::getContainer();
	}

	public function tearDown() {
		parent::setUp();

		unset( $this->container['engine_mockup'] );
		unset( $this->container );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getBindings
	 */
	public function testConstruct_Bindings_Accepted() {
		$expected = ['.foo' => 'foo', '.bar' => 'bar'];

		$subject = new NameProxyViewEngine( $expected );

		$this->assertEquals( $expected, $subject->getBindings() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getDefaultBinding
	 */
	public function testConstruct_Default_Accepted() {
		$expected = 'foo';

		$subject = new NameProxyViewEngine( [], $expected );

		$this->assertEquals( $expected, $subject->getDefaultBinding() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getDefaultBinding
	 */
	public function testConstruct_EmptyDefault_Ignored() {
		$subject = new NameProxyViewEngine( [], '' );

		$this->assertNotEquals( '', $subject->getDefaultBinding() );
	}

	/**
	 * @covers ::getBindingForFile
	 */
	public function testGetBindingForFile() {
		$subject = new NameProxyViewEngine( [
			'.blade.php' => 'blade',
			'.twig.php' => 'twig',
		], 'default' );

		$this->assertEquals( 'blade', $subject->getBindingForFile( 'test.blade.php' ) );
		$this->assertEquals( 'twig', $subject->getBindingForFile( 'test.twig.php' ) );
		$this->assertEquals( 'default', $subject->getBindingForFile( 'test.php' ) );
	}

	/**
	 * @covers ::exists
	 */
	public function testExists() {
		$view = 'foo';
		$this->container['engine_mockup'] = function() use ( $view ) {
			$mock = Mockery::mock();

			$mock->shouldReceive( 'exists' )
				->with( $view )
				->andReturn( true )
				->ordered();

			return $mock;
		};

		$subject = new NameProxyViewEngine( [], 'engine_mockup' );

		$this->assertTrue( $subject->exists( $view ) );
	}

	/**
	 * @covers ::canonical
	 */
	public function testCanonical() {
		$view = 'foo';
		$expected = 'foo.php';

		$this->container['engine_mockup'] = function() use ( $view, $expected ) {
			$mock = Mockery::mock();

			$mock->shouldReceive( 'canonical' )
				->with( $view )
				->andReturn( $expected )
				->ordered();

			return $mock;
		};

		$subject = new NameProxyViewEngine( [], 'engine_mockup' );

		$this->assertEquals( $expected, $subject->canonical( $view ) );
	}

	/**
	 * @covers ::make
	 */
	public function testMake() {
		$view = 'file.php';
		$result = 'foobar';

		$this->container['engine_mockup'] = function() use ( $view, $result ) {
			$mock = Mockery::mock();

			$mock->shouldReceive( 'exists' )
				->with( $view )
				->andReturn( true );

			$mock->shouldReceive( 'make' )
				->with( [$view] )
				->andReturn( $result );

			return $mock;
		};

		$subject = new NameProxyViewEngine( [], 'engine_mockup' );

		$this->assertEquals( $result, $subject->make( [$view] ) );
	}

	/**
	 * @covers ::make
	 * @expectedException \WPEmerge\Exceptions\ViewException
	 * @expectedExceptionMessage View not found
	 */
	public function testMake_NoView_EmptyString() {
		$view = '';

		$this->container['engine_mockup'] = function() use ( $view ) {
			$mock = Mockery::mock();

			$mock->shouldReceive( 'exists' )
				->with( $view )
				->andReturn( false );

			return $mock;
		};

		$subject = new NameProxyViewEngine( [], 'engine_mockup' );

		$subject->make( [$view] );
	}
}
