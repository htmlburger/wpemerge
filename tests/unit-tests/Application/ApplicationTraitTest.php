<?php

namespace WPEmergeTests\Application;

use BadMethodCallException;
use Mockery;
use WPEmerge\Application\ApplicationTrait;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\ApplicationTrait
 */
class ApplicationTraitTest extends TestCase {
	public function tear_down() {
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
		FooApp::traitTestMagicMethod();
	}

	/**
	 * @covers ::__callStatic
	 */
	public function testCallStatic_Method_MethodCalled() {
		FooApp::make();
		FooApp::alias( 'traitTestMagicMethod', function () { return 'foo'; } );

		$this->assertTrue( FooApp::hasAlias( 'traitTestMagicMethod' ) );
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
