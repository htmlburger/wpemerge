<?php

namespace WPEmergeTests\Routing\Conditions;

use Mockery;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Routing\Conditions\UrlCondition;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Conditions\UrlCondition
 */
class UrlConditionTest extends TestCase {
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
	 * @covers ::concatenate
	 */
	public function testConcatenate_Url() {
		$expected = '/foo/bar/';

		$subject1 = new UrlCondition( 'foo' );
		$subject2 = $subject1->concatenate( 'bar' );
		$this->assertEquals( $expected, $subject2->getUrl() );
		$this->assertNotSame( $subject2, $subject1 );

		$subject1 = new UrlCondition( '/foo' );
		$subject2 = $subject1->concatenate( '/bar' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( 'foo/' );
		$subject2 = $subject1->concatenate( 'bar/' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( 'foo/' );
		$subject2 = $subject1->concatenate( '/bar' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( '/foo' );
		$subject2 = $subject1->concatenate( 'bar/' );
		$this->assertEquals( $expected, $subject2->getUrl() );

		$subject1 = new UrlCondition( '/foo/' );
		$subject2 = $subject1->concatenate( '/bar/' );
		$this->assertEquals( $expected, $subject2->getUrl() );
	}

	/**
	 * @covers ::concatenate
	 */
	public function testConcatenate_Where() {
		$expected1 = ['foo' => 'foo'];
		$expected2 = ['bar' => 'bar'];
		$expected = ['foo' => 'foo', 'bar' => 'bar'];

		$subject = new UrlCondition( '', $expected1 );
		$subject = $subject->concatenate( '', $expected2 );

		$this->assertEquals( $expected, $subject->getUrlWhere() );
	}

	/**
	 * @covers ::concatenate
	 */
	public function testConcatenate_Wildcard_WildcardPersists() {
		$expected = UrlCondition::WILDCARD;

		$subject = new UrlCondition( UrlCondition::WILDCARD );
		$subject = $subject->concatenate( 'bar' );
		$this->assertEquals( $expected, $subject->getUrl() );

		$subject = new UrlCondition( 'foo' );
		$subject = $subject->concatenate( UrlCondition::WILDCARD );
		$this->assertEquals( $expected, $subject->getUrl() );

		$subject = new UrlCondition( UrlCondition::WILDCARD );
		$subject = $subject->concatenate( UrlCondition::WILDCARD );
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
	 * @covers ::getValidationPattern
	 * @covers ::replacePatternParameterWithPlaceholder
	 */
	public function testGetValidationPattern() {
		$subject1 = new UrlCondition( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2/3/?$~', $subject1->getValidationPattern( $subject1->getUrl() ) );

		$subject2 = new UrlCondition( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( '^/foo/bar/baz/1/2/3/?$', $subject2->getValidationPattern( $subject2->getUrl(), false ) );

		$subject3 = new UrlCondition( '/foo/{param1}/baz/1/2/3/' );
		$this->assertEquals( '~^/foo/(?P<param1>[^/]+)/baz/1/2/3/?$~', $subject3->getValidationPattern( $subject3->getUrl() ) );

		$subject4 = new UrlCondition( '/foo/bar/baz/1/2/{param1?}/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2(?:/(?P<param1>[^/]+))?/?$~', $subject4->getValidationPattern( $subject4->getUrl() ) );

		$subject4 = new UrlCondition( '/foo/bar/baz/1/2/{param1?}/{param2?}/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2(?:/(?P<param1>[^/]+))?(?:/(?P<param2>[^/]+))?/?$~', $subject4->getValidationPattern( $subject4->getUrl() ) );

		$subject5 = new UrlCondition( '/foo/{param1}/baz/{param2}/2/{param3?}/' );
		$this->assertEquals( '~^/foo/(?P<param1>[^/]+)/baz/(?P<param2>[^/]+)/2(?:/(?P<param3>[^/]+))?/?$~', $subject5->getValidationPattern( $subject5->getUrl() ) );

	}

	/**
	 * @covers ::toUrl
	 */
	public function testToUrl() {
		$subject = new UrlCondition( '' );
		$this->assertEquals( home_url( '/' ), $subject->toUrl() );

		$subject = new UrlCondition( '/' );
		$this->assertEquals( home_url( '/' ), $subject->toUrl() );

		$subject = new UrlCondition( '/{arg?}' );
		$this->assertEquals( home_url( '/' ), $subject->toUrl() );
		$this->assertEquals( home_url( '/foo' ), $subject->toUrl( ['arg' => 'foo'] ) );

		$subject = new UrlCondition( '/base/{arg1}' );
		$this->assertEquals( home_url( '/base/foo' ), $subject->toUrl( ['arg1' => 'foo'] ) );

		$subject = new UrlCondition( '/base/{arg1}/{arg2}' );
		$this->assertEquals( home_url( '/base/foo/bar' ), $subject->toUrl( ['arg1' => 'foo', 'arg2' => 'bar'] ) );

		$subject = new UrlCondition( '/base/{arg1}/{arg2?}/{arg3}' );
		$this->assertEquals( home_url( '/base/foo/bar/baz' ), $subject->toUrl( ['arg1' => 'foo', 'arg2' => 'bar', 'arg3' => 'baz'] ) );
		$this->assertEquals( home_url( '/base/foo/baz' ), $subject->toUrl( ['arg1' => 'foo', 'arg3' => 'baz'] ) );

		$subject = new UrlCondition( '/base/{arg1}/{arg2?}/mid/{arg3}/{arg4?}' );
		$this->assertEquals( home_url( '/base/foo/mid/baz' ), $subject->toUrl( ['arg1' => 'foo', 'arg3' => 'baz'] ) );
		$this->assertEquals( home_url( '/base/foo/bar/mid/baz' ), $subject->toUrl( ['arg1' => 'foo', 'arg2' => 'bar', 'arg3' => 'baz'] ) );
		$this->assertEquals( home_url( '/base/foo/mid/baz/fbz' ), $subject->toUrl( ['arg1' => 'foo', 'arg3' => 'baz', 'arg4' => 'fbz'] ) );
		$this->assertEquals( home_url( '/base/foo/bar/mid/baz/fbz' ), $subject->toUrl( ['arg1' => 'foo', 'arg2' => 'bar', 'arg3' => 'baz', 'arg4' => 'fbz'] ) );
	}

	/**
	 * @covers ::toUrl
	 * @expectedException \WPEmerge\Exceptions\ConfigurationException
	 * @expectedExceptionMessage Required URL parameter
	 */
	public function testToUrl_MissingArgument_Exception() {
		$subject = new UrlCondition( '/{arg1}' );
		$subject->toUrl();
	}
}
