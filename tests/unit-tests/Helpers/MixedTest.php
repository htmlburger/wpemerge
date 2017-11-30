<?php

use Obsidian\Helpers\Mixed;
use ObsidianTestTools\TestService;

/**
 * @coversDefaultClass \Obsidian\Helpers\Mixed
 */
class MixedTest extends WP_UnitTestCase {
	public function callableStub( $message = 'foobar' ) {
		return $message;
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArray_String_ReturnArrayContainingString() {
		$parameter = 'foobar';
		$expected = [$parameter];

		$this->assertEquals( $expected, Mixed::toArray( $parameter ) );
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArray_Array_ReturnSameArray() {
		$expected = ['foobar'];

		$this->assertEquals( $expected, Mixed::toArray( $expected ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_Callable_CallAndReturn() {
		$callable = [$this, 'callableStub'];
		$expected = 'foobar';

		$this->assertEquals( $expected, Mixed::value( $callable ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_CallableWithArguments_CallAndReturn() {
		$callable = [$this, 'callableStub'];
		$expected = 'hello world';

		$this->assertEquals( $expected, Mixed::value( $callable, [$expected] ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_Instance_CallInstanceMethodAndReturn() {
		$expected = 'foobar';

		$this->assertEquals( $expected, Mixed::value( $this, [], 'callableStub' ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_ClassName_CreateInstanceCallMethodAndReturn() {
		$expected = 'foobar';

		$this->assertEquals( $expected, Mixed::value( TestService::class, [], 'getTest' ) );
	}

	/**
	 * @covers ::value
	 */
	public function testValue_Other_ReturnSame() {
		$expected = 'someStringThatIsNotACallable';

		$this->assertSame( $expected, Mixed::value( $expected ) );
	}

	/**
	 * @covers ::isClass
	 */
	public function testIsClass() {
		$this->assertTrue( Mixed::isClass( 'stdClass' ) );
		$this->assertTrue( Mixed::isClass( TestService::class ) );
		$this->assertFalse( Mixed::isClass( 'NonExistantClassName' ) );
		$this->assertFalse( Mixed::isClass( 1 ) );
		$this->assertFalse( Mixed::isClass( new stdClass() ) );
		$this->assertFalse( Mixed::isClass( [] ) );
	}
}
