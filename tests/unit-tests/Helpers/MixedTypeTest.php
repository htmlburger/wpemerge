<?php

namespace WPEmergeTests\Helpers;

use WPEmerge\Helpers\MixedType;
use WPEmergeTestTools\TestService;
use stdClass;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Helpers\MixedType
 */
class MixedTypeTest extends WP_UnitTestCase {
	public function callableStub( $message = 'foobar' ) {
		return $message;
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArray_String_ReturnArrayContainingString() {
		$parameter = 'foobar';
		$expected = [$parameter];

		$this->assertEquals( $expected, MixedType::toArray( $parameter ) );
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArray_Array_ReturnSameArray() {
		$expected = ['foobar'];

		$this->assertEquals( $expected, MixedType::toArray( $expected ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_Callable_CallAndReturn() {
		$callable = [$this, 'callableStub'];
		$expected = 'foobar';

		$this->assertEquals( $expected, MixedType::value( $callable ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_CallableWithArguments_CallAndReturn() {
		$callable = [$this, 'callableStub'];
		$expected = 'hello world';

		$this->assertEquals( $expected, MixedType::value( $callable, [$expected] ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_Instance_CallInstanceMethodAndReturn() {
		$expected = 'foobar';

		$this->assertEquals( $expected, MixedType::value( $this, [], 'callableStub' ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_ClassName_CreateInstanceCallMethodAndReturn() {
		$expected = 'foobar';

		$this->assertEquals( $expected, MixedType::value( TestService::class, [], 'getTest' ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_Other_ReturnSame() {
		$expected = 'someStringThatIsNotACallable';

		$this->assertSame( $expected, MixedType::value( $expected ) );
	}

	/**
	 * @covers ::isClass
	 */
	public function testIsClass() {
		$this->assertTrue( MixedType::isClass( 'stdClass' ) );
		$this->assertTrue( MixedType::isClass( TestService::class ) );
		$this->assertFalse( MixedType::isClass( 'NonExistantClassName' ) );
		$this->assertFalse( MixedType::isClass( 1 ) );
		$this->assertFalse( MixedType::isClass( new stdClass() ) );
		$this->assertFalse( MixedType::isClass( [] ) );
	}

	/**
	 * @covers ::normalizePath
	 */
	public function testNormalizePath() {
		$ds = DIRECTORY_SEPARATOR;
		$input = '/foo\\bar/baz\\\\foobar';

		$this->assertEquals( "{$ds}foo{$ds}bar{$ds}baz{$ds}foobar", MixedType::normalizePath( $input ) );
		$this->assertEquals( '/foo/bar/baz/foobar', MixedType::normalizePath( $input, '/' ) );
		$this->assertEquals( '\\foo\\bar\\baz\\foobar', MixedType::normalizePath( $input, '\\' ) );
	}

	/**
	 * @covers ::addTrailingSlash
	 */
	public function testAddTrailingSlash() {
		$input = '/foo';

		$this->assertEquals( "/foo/", MixedType::addTrailingSlash( $input, '/' ) );
	}

	/**
	 * @covers ::removeTrailingSlash
	 */
	public function testRemoveTrailingSlash() {
		$input = '/foo/';

		$this->assertEquals( "/foo", MixedType::removeTrailingSlash( $input, '/' ) );
	}
}
