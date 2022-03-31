<?php

namespace WPEmergeTests\Application;

use Mockery;
use WPEmerge\Application\HasAliasesTrait;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Application\HasAliasesTrait
 */
class HasAliasesTraitTest extends TestCase {
	public $subject;

	public $resolver;

	public function set_up() {
		$this->resolver = Mockery::mock();
		$this->subject = new HasAliasesTraitTestImplementation();
		$this->subject->resolver = $this->resolver;
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->resolver );
		unset( $this->subject );
	}

	/**
	 * @covers ::hasAlias
	 */
	public function testHasAlias() {
		$this->assertFalse( $this->subject->hasAlias( 'foo' ) );
		$this->subject->alias( 'foo', 'bar' );
		$this->assertTrue( $this->subject->hasAlias( 'foo' ) );
	}

	/**
	 * @covers ::getAlias
	 */
	public function testGetAlias() {
		$this->assertNull( $this->subject->getAlias( 'foo' ) );
		$this->subject->alias( 'foo', 'bar', 'baz' );
		$this->assertEquals( [
			'name' => 'foo',
			'target' => 'bar',
			'method' => 'baz',
		], $this->subject->getAlias( 'foo' ) );
	}

	/**
	 * @covers ::setAlias
	 */
	public function testSetAlias_String_ResolveFromContainer() {
		$alias = 'test';
		$service_key = 'test_service';
		$service = new \WPEmergeTestTools\TestService();

		$this->resolver->shouldReceive( 'resolve' )
			->with( $service_key )
			->andReturn( $service );

		$this->subject->setAlias( [
			'name' => $alias,
			'target' => $service_key,
		] );

		$this->assertSame( $service, $this->subject->{$alias}() );
	}

	/**
	 * @covers ::setAlias
	 */
	public function testSetAlias_StringWithMethod_ResolveFromContainer() {
		$alias = 'test';
		$service_key = 'test_service';
		$service = new \WPEmergeTestTools\TestService();

		$this->resolver->shouldReceive( 'resolve' )
			->with( $service_key )
			->andReturn( $service );

		$this->subject->setAlias( [
			'name' => $alias,
			'target' => $service_key,
			'method' => 'getTest',
		] );

		$this->assertSame( 'foobar', $this->subject->{$alias}() );
	}

	/**
	 * @covers ::setAlias
	 */
	public function testSetAlias_Closure_CallClosure() {
		$expected = 'foo';
		$alias = 'test';
		$closure = function () use ( $expected ) {
			return $expected;
		};

		$this->subject->setAlias( [
			'name' => $alias,
			'target' => $closure,
		] );

		$this->assertEquals( $expected, $this->subject->{$alias}() );
	}
}

class HasAliasesTraitTestImplementation {
	use HasAliasesTrait;

	public $resolver = null;

	public function resolve( $key ) {
		return call_user_func_array( [$this->resolver, 'resolve'], func_get_args() );
	}
}
