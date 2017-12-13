<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge;
use WPEmerge\View\NameProxy;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\NameProxy
 */
class NameProxyTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->container = WPEmerge::getContainer();
	}

	public function tearDown() {
		parent::setUp();

		unset( $this->container );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getBindings
	 */
	public function testConstruct_Bindings_Accepted() {
		$expected = ['.foo' => 'foo', '.bar' => 'bar'];

		$subject = new NameProxy( $expected );

		$this->assertEquals( $expected, $subject->getBindings() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getDefaultBinding
	 */
	public function testConstruct_Default_Accepted() {
		$expected = 'foo';

		$subject = new NameProxy( [], $expected );

		$this->assertEquals( $expected, $subject->getDefaultBinding() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getDefaultBinding
	 */
	public function testConstruct_EmptyDefault_Ignored() {
		$subject = new NameProxy( [], '' );

		$this->assertNotEquals( '', $subject->getDefaultBinding() );
	}

	/**
	 * @covers ::getBindingForFile
	 */
	public function testGetBindingForFile() {
		$subject = new NameProxy( [
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

		$subject = new NameProxy( [], 'engine_mockup' );

		$this->assertTrue( $subject->exists( $view ) );
		unset( $this->container['engine_mockup'] );
	}

	/**
	 * @covers ::render
	 */
	public function testRender() {
		$view = 'file.php';
		$context = ['foo' => 'bar'];
		$result = 'foobar';

		$this->container['engine_mockup'] = function() use ( $view, $context, $result ) {
			$mock = Mockery::mock();

			$mock->shouldReceive( 'exists' )
				->with( $view )
				->andReturn( true );

			$mock->shouldReceive( 'render' )
				->with( [$view], $context )
				->andReturn( $result );

			return $mock;
		};

		$subject = new NameProxy( [], 'engine_mockup' );

		$this->assertEquals( $result, $subject->render( [$view], $context ) );
		unset( $this->container['engine_mockup'] );
	}

	/**
	 * @covers ::render
	 */
	public function testRender_NoView_EmptyString() {
		$view = '';

		$this->container['engine_mockup'] = function() use ( $view ) {
			$mock = Mockery::mock();

			$mock->shouldReceive( 'exists' )
				->with( $view )
				->andReturn( false );

			return $mock;
		};

		$subject = new NameProxy( [], 'engine_mockup' );

		$this->assertEquals( '', $subject->render( [$view], [] ) );
		unset( $this->container['engine_mockup'] );
	}
}
