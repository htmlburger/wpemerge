<?php

namespace WPEmergeTests\Templating;

use Mockery;
use WPEmerge;
use WPEmerge\Templating\FilenameProxy;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Templating\FilenameProxy
 */
class FilenameProxyTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->container = WPEmerge::getContainer();
	}

	/**
	 * @covers ::__construct
	 * @covers ::getBindings
	 */
	public function testConstruct_Bindings_Accepted() {
		$expected = ['.foo' => 'foo', '.bar' => 'bar'];

		$subject = new FilenameProxy( $expected );

		$this->assertEquals( $expected, $subject->getBindings() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getDefaultBinding
	 */
	public function testConstruct_Default_Accepted() {
		$expected = 'foo';

		$subject = new FilenameProxy( [], $expected );

		$this->assertEquals( $expected, $subject->getDefaultBinding() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getDefaultBinding
	 */
	public function testConstruct_EmptyDefault_Ignored() {
		$subject = new FilenameProxy( [], '' );

		$this->assertNotEquals( '', $subject->getDefaultBinding() );
	}

	/**
	 * @covers ::getBindingForFile
	 */
	public function testGetBindingForFile() {
		$subject = new FilenameProxy( [
			'.blade.php' => 'blade',
			'.twig.php' => 'twig',
		], 'default' );

		$this->assertEquals( 'blade', $subject->getBindingForFile( 'test.blade.php' ) );
		$this->assertEquals( 'twig', $subject->getBindingForFile( 'test.twig.php' ) );
		$this->assertEquals( 'default', $subject->getBindingForFile( 'test.php' ) );
	}

	/**
	 * @covers ::render
	 */
	public function testRender() {
		$file = 'file.php';
		$context = ['foo' => 'bar'];
		$result = 'foobar';

		$this->container['engine_mockup'] = function() use ( $file, $context, $result ) {
			$mock = Mockery::mock();
			$mock->shouldReceive( 'render' )
				->with( $file, $context )
				->andReturn( $result );
			return $mock;
		};

		$subject = new FilenameProxy( [], 'engine_mockup' );

		$this->assertEquals( $result, $subject->render( $file, $context ) );
	}
}
