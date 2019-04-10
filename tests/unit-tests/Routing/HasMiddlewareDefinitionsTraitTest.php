<?php

namespace WPEmergeTests\Routing;

use Closure;
use Mockery;
use WP_UnitTestCase;
use WPEmerge\Middleware\MiddlewareInterface;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\HasMiddlewareDefinitionsTrait;

/**
 * @coversDefaultClass \WPEmerge\Routing\HasMiddlewareDefinitionsTrait
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
	 * @covers ::expandMiddleware
	 */
	public function testExpandMiddleware() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short1' => 'long1',
			'short2' => 'long2',
		] );
		$subject->setMiddlewareGroups( [
			'group' => array(
				'short1',
				HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
			),
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
	public function testExpandMiddlewareGroup_Valid_ExpandedGroup() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );
		$subject->setMiddlewareGroups( [
			'group' => array(
				'short',
				HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
			),
		] );

		$this->assertEquals( [
			'long',
			HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
		], $subject->expandMiddlewareGroup( 'group' ) );
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

		$this->assertEquals( 'long', $subject->expandMiddlewareItem( 'short' ) );
	}

	/**
	 * @covers ::expandMiddlewareItem
	 */
	public function testExpandMiddlewareItem_Class_Unmodified() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		$this->assertEquals( HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class, $subject->expandMiddlewareItem( HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class ) );
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
