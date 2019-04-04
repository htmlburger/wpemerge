<?php

namespace WPEmergeTests\Routing\Conditions;

use Mockery;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\UrlCondition;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Conditions\UrlCondition
 */
class UrlConditionTest extends WP_UnitTestCase {
	/**
	 * @covers ::setUrl
	 */
	public function testSetUrl_String_AddLeadingAndTrailingSlashes() {
		$expected = '/foo/bar/';
		$subject = new UrlCondition( 'foo/bar' );

		$this->assertEquals( $expected, $subject->getUrl() );
	}

	/**
	 * @covers ::getUrl
	 * @covers ::setUrl
	 */
	public function testSetUrlWildcard_DoNotAddSlashes() {
		$expected = UrlCondition::WILDCARD;
		$subject = new UrlCondition( UrlCondition::WILDCARD );

		$this->assertEquals( $expected, $subject->getUrl() );
	}

	/**
	 * @covers ::getUrl
	 */
	public function testGetUrl() {
		$subject1 = new UrlCondition( '' );
		$this->assertEquals( '/', $subject1->getUrl() );

		$subject2 = new UrlCondition( 'foo' );
		$this->assertEquals( '/foo/', $subject2->getUrl() );

		$subject3 = new UrlCondition( '/foo' );
		$this->assertEquals( '/foo/', $subject3->getUrl() );

		$subject4 = new UrlCondition( 'foo/' );
		$this->assertEquals( '/foo/', $subject4->getUrl() );

		$subject5 = new UrlCondition( '/foo/' );
		$this->assertEquals( '/foo/', $subject5->getUrl() );
	}

	/**
	 * @covers ::getUrlWhere
	 * @covers ::setUrlWhere
	 */
	public function testGetUrlWhere() {
		$expected = ['foo' => 'bar'];
		$subject = new UrlCondition( '' );
		$subject->setUrlWhere( $expected );
		$this->assertEquals( $expected, $subject->getUrlWhere() );
	}

	/**
	 * @covers ::concatenateUrl
	 */
	public function testConcatenateUrl() {
		$expected = '/foo/bar/';

		$subject1 = new UrlCondition( 'foo' );
		$subject2 = $subject1->concatenateUrl( 'bar' );
		$this->assertEquals( $expected, $subject2->getUrl() );
		$this->assertNotSame( $subject2, $subject1 );

		$subject1 = new UrlCondition( '/foo' );
		$subject2 = $subject1->concatenateUrl( '/bar' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( 'foo/' );
		$subject2 = $subject1->concatenateUrl( 'bar/' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( 'foo/' );
		$subject2 = $subject1->concatenateUrl( '/bar' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( '/foo' );
		$subject2 = $subject1->concatenateUrl( 'bar/' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( '/foo/' );
		$subject2 = $subject1->concatenateUrl( '/bar/' );
		$this->assertEquals( $expected, $subject2->getUrl() );
	}

	/**
	 * @covers ::concatenateUrl
	 */
	public function testConcatenateUrl_Wildcard_WildcardPersists() {
		$expected = UrlCondition::WILDCARD;

		$subject = new UrlCondition( UrlCondition::WILDCARD );
		$subject = $subject->concatenateUrl( 'bar' );
		$this->assertEquals( $expected, $subject->getUrl() );

		$subject = new UrlCondition( 'foo' );
		$subject = $subject->concatenateUrl( UrlCondition::WILDCARD );
		$this->assertEquals( $expected, $subject->getUrl() );

		$subject = new UrlCondition( UrlCondition::WILDCARD );
		$subject = $subject->concatenateUrl( UrlCondition::WILDCARD );
		$this->assertEquals( $expected, $subject->getUrl() );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_Url() {
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/' );

		$subject1 = new UrlCondition( '/foo/bar' );
		$this->assertTrue( $subject1->isSatisfied( $request ) );

		$subject2 = new UrlCondition( '/foo/bar/' );
		$this->assertTrue( $subject2->isSatisfied( $request ) );

		$subject3 = new UrlCondition( '/foo/{param1}/' );
		$this->assertTrue( $subject3->isSatisfied( $request ) );

		$subject4 = new UrlCondition( '/foo/bar/{param1?}/' );
		$this->assertTrue( $subject4->isSatisfied( $request ) );

		$subject6 = new UrlCondition( '/foo/' );
		$this->assertFalse( $subject6->isSatisfied( $request ) );

		$subject7 = new UrlCondition( '/foo/bar/baz/' );
		$this->assertFalse( $subject7->isSatisfied( $request ) );

		$subject5 = new UrlCondition( '/foo/{param1:\d+}/' );
		$this->assertFalse( $subject5->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 * @covers ::whereIsSatisfied
	 */
	public function testIsSatisfied_Where() {
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/' );

		$subject = new UrlCondition( '/{param1}/{param2}' );
		$this->assertTrue( $subject->isSatisfied( $request ) );

		$subject = new UrlCondition( '/{param1}/{param2}' );
		$subject->setUrlWhere( [
			'param1' => '/^[fobar]+$/i',
		] );
		$this->assertTrue( $subject->isSatisfied( $request ) );

		$subject = new UrlCondition( '/{param1}/{param2}' );
		$subject->setUrlWhere( [
			'param1' => '/^[fobar]+$/i',
			'param2' => '/^[fobar]+$/i',
		] );
		$this->assertTrue( $subject->isSatisfied( $request ) );

		$subject = new UrlCondition( '/{param1}/{param2}' );
		$subject->setUrlWhere( [
			'param1' => '/^\d+$/i',
		] );
		$this->assertFalse( $subject->isSatisfied( $request ) );

		$subject = new UrlCondition( '/{param1}/{param2}' );
		$subject->setUrlWhere( [
			'param2' => '/^\d+$/i',
		] );
		$this->assertFalse( $subject->isSatisfied( $request ) );

		$subject = new UrlCondition( '/{param1}/{param2}' );
		$subject->setUrlWhere( [
			'param1' => '/^\d+$/i',
			'param2' => '/^[fobar]+$/i',
		] );
		$this->assertFalse( $subject->isSatisfied( $request ) );

		$subject = new UrlCondition( '/{param1}/{param2}' );
		$subject->setUrlWhere( [
			'param1' => '/^[fobar]+$/i',
			'param2' => '/^\d+$/i',
		] );
		$this->assertFalse( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_Wildcard_True() {
		$request = Mockery::mock( RequestInterface::class );
		$subject = new UrlCondition( UrlCondition::WILDCARD );

		$this->assertTrue( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 * @covers ::getParameterNames
	 */
	public function testGetArguments() {
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/baz/1/2/3/' );

		$subject1 = new UrlCondition( '/doesn\'tmatch/' );
		$this->assertEquals( [], $subject1->getArguments( $request ) );

		$subject2 = new UrlCondition( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( [], $subject2->getArguments( $request ) );

		$subject3 = new UrlCondition( '/foo/{param1}/baz/1/2/3/' );
		$this->assertEquals( ['param1' => 'bar'], $subject3->getArguments( $request ) );

		$subject4 = new UrlCondition( '/foo/bar/baz/1/2/{param1?}/' );
		$this->assertEquals( ['param1' => '3'], $subject4->getArguments( $request ) );

		$subject5 = new UrlCondition( '/foo/bar/baz/1/2/3/{param1?}/' );
		$this->assertEquals( ['param1' => ''], $subject5->getArguments( $request ) );

		$subject6 = new UrlCondition( '/foo/{param1}/baz/{param2}/2/3/{param3?}/' );
		$this->assertEquals( ['param1' => 'bar', 'param2' => '1', 'param3' => ''], $subject6->getArguments( $request ) );
	}

	/**
	 * @covers ::getValidationRegex
	 * @covers ::replaceRegexParameterWithPlaceholder
	 */
	public function testGetValidationRegex() {
		$subject1 = new UrlCondition( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2/3/?$~', $subject1->getValidationRegex( $subject1->getUrl() ) );

		$subject2 = new UrlCondition( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( '^/foo/bar/baz/1/2/3/?$', $subject2->getValidationRegex( $subject2->getUrl(), false ) );

		$subject3 = new UrlCondition( '/foo/{param1}/baz/1/2/3/' );
		$this->assertEquals( '~^/foo/(?P<param1>[^/]+)/baz/1/2/3/?$~', $subject3->getValidationRegex( $subject3->getUrl() ) );

		$subject4 = new UrlCondition( '/foo/bar/baz/1/2/{param1?}/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2(?:/(?P<param1>[^/]+))?/?$~', $subject4->getValidationRegex( $subject4->getUrl() ) );

		$subject4 = new UrlCondition( '/foo/bar/baz/1/2/{param1?}/{param2?}/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2(?:/(?P<param1>[^/]+))?(?:/(?P<param2>[^/]+))?/?$~', $subject4->getValidationRegex( $subject4->getUrl() ) );

		$subject5 = new UrlCondition( '/foo/{param1}/baz/{param2}/2/{param3?}/' );
		$this->assertEquals( '~^/foo/(?P<param1>[^/]+)/baz/(?P<param2>[^/]+)/2(?:/(?P<param3>[^/]+))?/?$~', $subject5->getValidationRegex( $subject5->getUrl() ) );

	}
}
