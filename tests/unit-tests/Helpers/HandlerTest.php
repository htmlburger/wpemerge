<?php

namespace WPEmergeTests\Helpers;

use Mockery;
use stdClass;
use WPEmerge\Application\GenericFactory;
use WPEmerge\Exceptions\ClassNotFoundException;
use WPEmerge\Helpers\Handler;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Helpers\Handler
 */
class HandlerTest extends TestCase {
	public function set_up() {
		$this->factory = Mockery::mock( GenericFactory::class );

		$this->factory->shouldReceive( 'make' )
			->andReturnUsing( function ( $class ) {
				if ( ! class_exists( $class ) ) {
					throw new ClassNotFoundException();
				}

				return new $class();
			} );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->factory );
	}

	/**
	 * @covers ::__construct
	 * @covers ::get
	 */
	public function testConstruct() {
		$expected = function() {};

		$subject = new Handler( $this->factory, $expected );

		$this->assertSame( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 */
	public function testParse_Closure_Closure() {
		$expected = function() {};

		$subject = new Handler( $this->factory, $expected );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage No or invalid handler
	 */
	public function testParseFromArray_EmptyArray_Exception() {
		new Handler( $this->factory, [] );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage No or invalid handler
	 */
	public function testParseFromArray_MalformedArray_Exception() {
		new Handler( $this->factory, ['', \WPEmergeTestTools\TestService::class, 'foo'] );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage No or invalid handler
	 */
	public function testParseFromArray_FQCNWithoutMethod_Exception() {
		new Handler( $this->factory, [\WPEmergeTestTools\TestService::class] );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 */
	public function testParseFromArray_FQCNWithLeadingBackslash_LeadingBackslashTrimmed() {
		$expected = [
			'class' => 'WPEmergeTestTools\\TestService',
			'method' => 'foo',
			'namespace' => '',
		];

		$subject = new Handler( $this->factory, ['\\WPEmergeTestTools\\TestService', 'foo'] );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 */
	public function testParseFromArray_FQCNWithoutMethodWithDefault_Array() {
		$expected = [
			'class' => \WPEmergeTestTools\TestService::class,
			'method' => 'defaultMethod',
			'namespace' => '',
		];

		$subject = new Handler( $this->factory, [\WPEmergeTestTools\TestService::class], 'defaultMethod' );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage No or invalid handler
	 */
	public function testParseFromArray_FQCNWithEmptyMethod_Exception() {
		new Handler( $this->factory, [\WPEmergeTestTools\TestService::class, ''] );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 */
	public function testParseFromArray_FQCNWithMethod_Array() {
		$expected = [
			'class' => \WPEmergeTestTools\TestService::class,
			'method' => 'foo',
			'namespace' => '',
		];

		$subject = new Handler( $this->factory, [\WPEmergeTestTools\TestService::class, 'foo'] );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 */
	public function testParseFromArray_ClassWithNamespace_Array() {
		$expected = [
			'class' => 'TestService',
			'method' => 'defaultMethod',
			'namespace' => 'WPEmergeTestTools\\',
		];

		$subject = new Handler( $this->factory, ['TestService'], 'defaultMethod', 'WPEmergeTestTools\\' );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromArray
	 */
	public function testParseFromArray_FQCNWithNamespace_Array() {
		$expected = [
			'class' => 'TestService',
			'method' => 'defaultMethod',
			'namespace' => 'WPEmergeTestTools\\',
		];

		$subject = new Handler( $this->factory, [\TestService::class], 'defaultMethod', 'WPEmergeTestTools\\' );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromString
	 */
	public function testParseFromString_ClassWithoutMethodWithDefault_Array() {
		$expected = [
			'class' => 'WPEmergeTestTools\\TestService',
			'method' => 'defaultMethod',
			'namespace' => '',
		];

		$subject = new Handler( $this->factory, 'WPEmergeTestTools\\TestService', 'defaultMethod' );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromString
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage No or invalid handler
	 */
	public function testParseFromString_ClassWithoutMethodWithoutDefault_Exception() {
		new Handler( $this->factory, 'WPEmergeTestTools\\TestService' );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromString
	 */
	public function testParseFromString_ClassAtMethod_Array() {
		$expected = [
			'class' => 'WPEmergeTestTools\\TestService',
			'method' => 'getTest',
			'namespace' => '',
		];

		$subject = new Handler( $this->factory, 'WPEmergeTestTools\\TestService@getTest' );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::parse
	 * @covers ::parseFromString
	 */
	public function testParseFromString_ClassColonsMethod_Array() {
		$expected = [
			'class' => 'WPEmergeTestTools\\TestService',
			'method' => 'getTest',
			'namespace' => '',
		];

		$subject = new Handler( $this->factory, 'WPEmergeTestTools\\TestService::getTest' );

		$this->assertEquals( $expected, $subject->get() );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_Closure_ReturnSame() {
		$expected = function() {};
		$subject = new Handler( $this->factory, $expected );
		$this->assertSame( $expected, $subject->make() );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_ClassWithoutPrefix_Instance() {
		$subject = new Handler( $this->factory, 'WPEmergeTests\\Helpers\\HandlerTestMock@foo' );
		$this->assertInstanceOf( \WPEmergeTests\Helpers\HandlerTestMock::class, $subject->make() );
	}

	/**
	 * @covers ::make
	 */
	public function testMake_ClassWithPrefix_Instance() {
		$subject = new Handler( $this->factory, 'HandlerTestMock@foo', '', 'WPEmergeTests\\Helpers\\' );
		$this->assertInstanceOf( \WPEmergeTests\Helpers\HandlerTestMock::class, $subject->make() );
	}

	/**
	 * @covers ::make
	 * @expectedException \WPEmerge\Exceptions\ClassNotFoundException
	 * @expectedExceptionMessage Class not found
	 */
	public function testMake_NonexistentClassWithPrefix_Exception() {
		$subject = new Handler( $this->factory, 'HandlerTestMock@foo', '', 'WPEmergeTests\\NonexistentNamespace\\' );
		$subject->make();
	}

	/**
	 * @covers ::execute
	 */
	public function testExecute_Closure_CalledWithArguments() {
		$stub = new stdClass();
		$mock = Mockery::mock();
		$mock->shouldReceive( 'execute' )
			->with( $mock, $stub )
			->once();

		$closure = function( $mock, $stub ) {
			$mock->execute( $mock, $stub );
		};

		$subject = new Handler( $this->factory, $closure );
		$subject->execute( $mock, $stub );
		$this->assertTrue( true );
	}

	/**
	 * @covers ::execute
	 */
	public function testExecute_ClassAtMethod_CalledWithArguments() {
		$foo = 'foo';
		$bar = 'bar';
		$expected = (object) ['value' => $foo . $bar];

		$subject = new Handler( $this->factory, HandlerTestControllerMock::class . '@foobar' );
		$this->assertEquals( $expected, $subject->execute( 'foo', 'bar' ) );
	}
}

class HandlerTestMock {
	public function foo() {
		return 'foo';
	}
}

class HandlerTestControllerMock {
	public function foobar( $foo, $bar ) {
		return (object) ['value' => $foo . $bar];
	}
}
