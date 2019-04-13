<?php

namespace WPEmergeTests\Routing;

use Closure;
use Mockery;
use WP_UnitTestCase;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Middleware\HasMiddlewareDefinitionsTrait;
use WPEmerge\Requests\RequestInterface;

/**
 * @coversDefaultClass \WPEmerge\Middleware\HasMiddlewareDefinitionsTrait
 */
class HasMiddlewareDefinitionsTraitTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	/**
	 * @covers ::uniqueMiddleware
	 */
	public function testUniqueMiddleware() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		// While this nested syntax is not yet supported we don't want its
		// introduction to cause backwards compatibility problems.
		$this->assertEquals(
			[
				'foo',
				['foo', 1],
				['foo', 2]
			],
			$subject->uniqueMiddleware( [
				'foo',
				['foo', 1],
				'foo',
				['foo', 1],
				['foo', 2]
			] )
		);
	}

	/**
	 * @covers ::expandMiddleware
	 */
	public function testExpandMiddleware() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short1' => 'long1',
			'short2' => 'long2',
		] );
		$subject->setMiddlewareGroups( [
			'group' => [
				'short1',
				HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
			],
		] );

		$this->assertEquals( [
			'long2',
			'long1',
			HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
		], $subject->expandMiddleware( ['short2', 'group'] ) );
	}

	/**
	 * @covers ::expandMiddlewareGroup
	 */
	public function testExpandMiddlewareGroup_Predefined_HasGlobalPrepended() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
			'global-short' => 'global-long',
		] );
		$subject->setMiddlewareGroups( [
			'global' => ['global-short'],
			'web' => [],
			'admin' => ['short'],
			'ajax' => ['admin'],
		] );

		$this->assertEquals( ['global-long'], $subject->expandMiddlewareGroup( 'web' ) );
		$this->assertEquals( ['global-long', 'long'], $subject->expandMiddlewareGroup( 'admin' ) );
		$this->assertEquals( ['global-long', 'global-long', 'long'], $subject->expandMiddlewareGroup( 'ajax' ) );
	}

	/**
	 * @covers ::expandMiddlewareGroup
	 */
	public function testExpandMiddlewareGroup_Nested_RecursivelyExpandedGroup() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );
		$subject->setMiddlewareGroups( [
			'group1' => ['short'],
			'group2' => ['group1'],
			'group3' => ['group2'],
		] );

		$this->assertEquals( ['long'], $subject->expandMiddlewareGroup( 'group3' ) );
	}

	/**
	 * @covers ::expandMiddlewareGroup
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage Unknown middleware group
	 */
	public function testExpandMiddlewareGroup_Invalid_Exception() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->expandMiddlewareGroup( 'undefined group' );
	}

	/**
	 * @covers ::expandMiddlewareItem
	 */
	public function testExpandMiddlewareItem_DefinedString_Expanded() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );

		$this->assertEquals( ['long'], $subject->expandMiddlewareItem( 'short' ) );
	}

	/**
	 * @covers ::expandMiddlewareItem
	 */
	public function testExpandMiddlewareItem_Class_Unmodified() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		$this->assertEquals( [HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class], $subject->expandMiddlewareItem( HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class ) );
	}

	/**
	 * @covers ::expandMiddlewareItem
	 */
	public function testExpandMiddlewareItem_Group_Expanded() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );
		$subject->setMiddlewareGroups( [
			'group' => [
				'short',
				HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
			],
		] );

		$this->assertEquals( [
			'long',
			HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
		], $subject->expandMiddlewareItem( 'group' ) );
	}

	/**
	 * @covers ::expandMiddlewareItem
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage Unknown middleware
	 */
	public function testExpandMiddlewareItem_UndefinedString_Exception() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		$subject->expandMiddlewareItem( 'undefined middleware' );
	}
}

class HasMiddlewareDefinitionsTraitTestImplementation {
	use HasMiddlewareDefinitionsTrait;
}

class HasMiddlewareDefinitionsTraitTestMiddlewareStub1 implements MiddlewareInterface {
	public function handle( RequestInterface $request, Closure $next ) {}
}
