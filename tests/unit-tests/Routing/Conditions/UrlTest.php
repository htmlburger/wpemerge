<?php

namespace WPEmergeTests\Routing\Conditions;

use Mockery;
use WPEmerge\Requests\Request;
use WPEmerge\Routing\Conditions\Url;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Routing\Conditions\Url
 */
class UrlTest extends WP_UnitTestCase {
	/**
	 * @covers ::__construct
	 * @covers ::getUrl
	 */
	public function testConstruct_String_AddLeadingAndTrailingSlashes() {
		$expected = '/foo/bar/';
		$subject = new Url( 'foo/bar' );

		$this->assertEquals( $expected, $subject->getUrl() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::getUrl
	 */
	public function testConstruct_Wildcard_DoNotAddSlashes() {
		$expected = Url::WILDCARD;
		$subject = new Url( Url::WILDCARD );

		$this->assertEquals( $expected, $subject->getUrl() );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_Wildcard_True() {
		$request = Mockery::mock( Request::class );
		$subject = new Url( Url::WILDCARD );

		$this->assertTrue( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_Url() {
		$request = Mockery::mock( Request::class );

		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/' );

		$subject1 = new Url( '/foo/bar' );
		$this->assertTrue( $subject1->isSatisfied( $request ) );

		$subject2 = new Url( '/foo/bar/' );
		$this->assertTrue( $subject2->isSatisfied( $request ) );

		$subject3 = new Url( '/foo/{param1}/' );
		$this->assertTrue( $subject3->isSatisfied( $request ) );

		$subject4 = new Url( '/foo/bar/{param1?}/' );
		$this->assertTrue( $subject4->isSatisfied( $request ) );

		$subject6 = new Url( '/foo/' );
		$this->assertFalse( $subject6->isSatisfied( $request ) );

		$subject7 = new Url( '/foo/bar/baz/' );
		$this->assertFalse( $subject7->isSatisfied( $request ) );

		$subject5 = new Url( '/foo/{param1:\d+}/' );
		$this->assertFalse( $subject5->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 * @covers ::getParameterNames
	 */
	public function testGetArguments() {
		$request = Mockery::mock( Request::class );

		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/baz/1/2/3/' );

		$subject1 = new Url( '/doesn\'tmatch/' );
		$this->assertEquals( [], $subject1->getArguments( $request ) );

		$subject2 = new Url( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( [], $subject2->getArguments( $request ) );

		$subject3 = new Url( '/foo/{param1}/baz/1/2/3/' );
		$this->assertEquals( ['bar'], $subject3->getArguments( $request ) );

		$subject4 = new Url( '/foo/bar/baz/1/2/{param1?}/' );
		$this->assertEquals( ['3'], $subject4->getArguments( $request ) );

		$subject5 = new Url( '/foo/bar/baz/1/2/3/{param1?}/' );
		$this->assertEquals( [''], $subject5->getArguments( $request ) );

		$subject6 = new Url( '/foo/{param1}/baz/{param2}/2/3/{param3?}/' );
		$this->assertEquals( ['bar', '1', ''], $subject6->getArguments( $request ) );
	}

	/**
	 * @covers ::concatenate
	 */
	public function testConcatenate() {
		$expected = '/foo/bar/';

		$subject1 = new Url( '/foo/' );
		$subject2 = new Url( '/bar/' );

		$subject3 = $subject1->concatenate( $subject2 );
		$this->assertEquals( $expected, $subject3->getUrl() );
		$this->assertNotSame( $subject3, $subject1 );
		$this->assertNotSame( $subject3, $subject2 );
	}

	/**
	 * @covers ::getValidationRegex
	 * @covers ::replaceRegexParameterWithPlaceholder
	 */
	public function testGetValidationRegex() {
		$subject1 = new Url( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2/3/?$~', $subject1->getValidationRegex( $subject1->getUrl() ) );

		$subject2 = new Url( '/foo/bar/baz/1/2/3/' );
		$this->assertEquals( '^/foo/bar/baz/1/2/3/?$', $subject2->getValidationRegex( $subject2->getUrl(), false ) );

		$subject3 = new Url( '/foo/{param1}/baz/1/2/3/' );
		$this->assertEquals( '~^/foo/(?P<param1>[^/]+)/baz/1/2/3/?$~', $subject3->getValidationRegex( $subject3->getUrl() ) );

		$subject3 = new Url( '/foo/bar/baz/1/2/{param1:\d+}/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2/(?P<param1>\d+)/?$~', $subject3->getValidationRegex( $subject3->getUrl() ) );

		$subject4 = new Url( '/foo/bar/baz/1/2/{param1?:\d+}/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2(?:/(?P<param1>\d+))?/?$~', $subject4->getValidationRegex( $subject4->getUrl() ) );

		$subject4 = new Url( '/foo/bar/baz/1/2/{param1?:\d+}/{param2?:\d+}/' );
		$this->assertEquals( '~^/foo/bar/baz/1/2(?:/(?P<param1>\d+))?(?:/(?P<param2>\d+))?/?$~', $subject4->getValidationRegex( $subject4->getUrl() ) );

		$subject5 = new Url( '/foo/{param1}/baz/{param2:\d+}/2/{param3?}/' );
		$this->assertEquals( '~^/foo/(?P<param1>[^/]+)/baz/(?P<param2>\d+)/2(?:/(?P<param3>[^/]+))?/?$~', $subject5->getValidationRegex( $subject5->getUrl() ) );

	}
}
