<?php

namespace WPEmergeTests\Application;

use BadMethodCallException;
use Mockery;
use WPEmerge\Application\ApplicationTrait;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\ApplicationTrait
 */
class ApplicationTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		FooApp::setApplication( null );
		BarApp::setApplication( null );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_NullInstance_NewInstance() {
		$this->assertNull( FooApp::getApplication() );
		$app = FooApp::make();
		$this->assertSame( $app, FooApp::getApplication() );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_OldInstance_NewInstance() {
		$old = FooApp::make();
		$this->assertSame( $old, FooApp::getApplication() );
		$new = FooApp::make();
		$this->assertSame( $new, FooApp::getApplication() );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_MultipleApps_DifferentInstances() {
		$this->assertNull( FooApp::getApplication() );
		$this->assertNull( BarApp::getApplication() );

		$foo = FooApp::make();

		$this->assertSame( $foo, FooApp::getApplication() );
		$this->assertNull( BarApp::getApplication() );

		$bar = BarApp::make();

		$this->assertSame( $foo, FooApp::getApplication() );
		$this->assertSame( $bar, BarApp::getApplication() );
		$this->assertNotSame( FooApp::getApplication(), BarApp::getApplication() );
	}

	/**
	 * @covers ::__callStatic
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage Application instance not created
	 */
	public function testCallStatic_NullInstance_Exception() {
		FooApp::foo();
	}

	/**
	 * @covers ::__callStatic
	 * @expectedException BadMethodCallException
	 * @expectedExceptionMessage does not exist
	 */
	public function testCallStatic_InvalidMethod_Exception() {
		FooApp::make();
		FooApp::foo();
	}

	/**
	 * @covers ::__callStatic
	 */
	public function testCallStatic_Method_MethodCalled() {
		FooApp::make();
		FooApp::alias( 'test', function () { return 'foo'; } );

		$this->assertTrue( FooApp::hasAlias( 'test' ) );
	}

	/**
	 * @covers ::__callStatic
	 */
	public function testCallStatic_MagicMethod_MethodCalled() {
		FooApp::make();
		FooApp::alias( 'traitTestMagicMethod', function () { return 'foo'; } );

		$this->assertSame( 'foo', FooApp::traitTestMagicMethod() );
	}
}

class FooApp {
	use ApplicationTrait;
}

class BarApp {
	use ApplicationTrait;
}
