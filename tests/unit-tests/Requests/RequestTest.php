<?php

namespace WPEmergeTests\Requests;

use WPEmerge\Requests\Request;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Requests\Request
 */
class RequestTest extends WP_UnitTestCase {
	/**
	 * @covers ::getMethod
	 * @covers ::getMethodOverride
	 */
	public function testGetMethod() {
		$expected = 'GET';
		$subject = new Request( $expected, '' );
		$this->assertEquals( $expected, $subject->getMethod() );

		$expected = 'POST';
		$subject = new Request( $expected, '' );
		$this->assertEquals( $expected, $subject->getMethod() );

		$expected = 'GET';
		$subject = new Request( 'GET', '', ['X-HTTP-METHOD-OVERRIDE' => ['PUT']] );
		$this->assertEquals( $expected, $subject->getMethod() );

		$expected = 'PUT';
		$subject = new Request( 'POST', '', ['X-HTTP-METHOD-OVERRIDE' => [$expected]] );
		$this->assertEquals( $expected, $subject->getMethod() );

		$expected = 'PUT';
		$subject = (new Request( 'POST', '' ))->withParsedBody( ['_method' => $expected] );
		$this->assertEquals( $expected, $subject->getMethod() );

		$expected = 'PUT';
		$subject = (new Request( 'POST', '', ['X-HTTP-METHOD-OVERRIDE' => ['PATCH']] ))->withParsedBody( ['_method' => $expected] );
		$this->assertEquals( $expected, $subject->getMethod() );
	}

	/**
	 * @covers ::isGet
	 * @covers ::isHead
	 * @covers ::isPost
	 * @covers ::isPut
	 * @covers ::isPatch
	 * @covers ::isDelete
	 * @covers ::isOptions
	 */
	public function testIsMethod() {
		$subject = new Request( 'GET', '' );
		$this->assertTrue( $subject->isGet() );

		$subject = new Request( 'HEAD', '' );
		$this->assertTrue( $subject->isHead() );

		$subject = new Request( 'POST', '' );
		$this->assertTrue( $subject->isPost() );

		$subject = new Request( 'PUT', '' );
		$this->assertTrue( $subject->isPut() );

		$subject = new Request( 'PATCH', '' );
		$this->assertTrue( $subject->isPatch() );

		$subject = new Request( 'DELETE', '' );
		$this->assertTrue( $subject->isDelete() );

		$subject = new Request( 'OPTIONS', '' );
		$this->assertTrue( $subject->isOptions() );
	}

	/**
	 * @covers ::isReadVerb
	 */
	public function testIsReadVerb() {
		$subject1 = new Request( 'GET', '' );
		$this->assertTrue( $subject1->isReadVerb() );

		$subject2 = new Request( 'HEAD', '' );
		$this->assertTrue( $subject2->isReadVerb() );

		$subject3 = new Request( 'OPTIONS', '' );
		$this->assertTrue( $subject3->isReadVerb() );

		$subject3 = new Request( 'POST', '' );
		$this->assertFalse( $subject3->isReadVerb() );

		$subject3 = new Request( 'PUT', '' );
		$this->assertFalse( $subject3->isReadVerb() );

		$subject3 = new Request( 'PATCH', '' );
		$this->assertFalse( $subject3->isReadVerb() );

		$subject3 = new Request( 'DELETE', '' );
		$this->assertFalse( $subject3->isReadVerb() );
	}

	/**
	 * @covers ::isAjax
	 */
	public function testIsAjax() {
		$subject = new Request( 'GET', '' );
		$this->assertFalse( $subject->isAjax() );

		$subject = new Request( 'GET', '', ['X-Requested-With' => ['XMLHttpRequest']] );
		$this->assertTrue( $subject->isAjax() );
	}

	/**
	 * @covers ::get
	 * @covers ::query
	 * @covers ::body
	 * @covers ::cookies
	 * @covers ::files
	 * @covers ::server
	 * @covers ::headers
	 */
	public function testGet_ExistingKey_ReturnValue() {
		$expected = 'foo';
		$key = 'key';

		$subject = (new Request( 'GET', '' ))->withAttribute( $key, $expected );
		$this->assertEquals( $expected, $subject->attributes( $key ) );

		$subject = (new Request( 'GET', '' ))->withQueryParams( [$key => $expected] );
		$this->assertEquals( $expected, $subject->query( $key ) );

		$subject = (new Request( 'POST', '' ))->withParsedBody( [$key => $expected] );
		$this->assertEquals( $expected, $subject->body( $key ) );

		$subject = (new Request( 'GET', '' ))->withCookieParams( [$key => $expected] );
		$this->assertEquals( $expected, $subject->cookies( $key ) );

		$subject = (new Request( 'POST', '' ))->withUploadedFiles([$key => $expected]);
		$this->assertEquals( $expected, $subject->files( $key ) );

		$subject = new Request( 'GET', '', [], null, '1.1', [$key => $expected] );
		$this->assertEquals( $expected, $subject->server( $key ) );

		$subject = new Request( 'GET', '', [$key => [$expected]] );
		$this->assertEquals( [$expected], $subject->headers( $key ) );
	}

	/**
	 * @covers ::get
	 * @covers ::query
	 * @covers ::body
	 * @covers ::cookies
	 * @covers ::files
	 * @covers ::server
	 * @covers ::headers
	 */
	public function testGet_NonExistantKey_ReturnNull() {
		$expected = null;
		$key = 'key';

		$subject = (new Request( 'GET', '' ));
		$this->assertEquals( $expected, $subject->attributes( $key ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->query( $key ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->body( $key ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->cookies( $key ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->files( $key ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->server( $key ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->headers( $key ) );
	}

	/**
	 * @covers ::get
	 * @covers ::query
	 * @covers ::body
	 * @covers ::cookies
	 * @covers ::files
	 * @covers ::server
	 * @covers ::headers
	 */
	public function testGet_NonExistantKeyWithDefault_ReturnDefault() {
		$expected = 'foo';
		$key = 'key';

		$subject = (new Request( 'GET', '' ));
		$this->assertEquals( $expected, $subject->attributes( $key, $expected ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->query( $key, $expected ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->body( $key, $expected ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->cookies( $key, $expected ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->files( $key, $expected ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->server( $key, $expected ) );

		$subject = new Request( 'GET', '' );
		$this->assertEquals( $expected, $subject->headers( $key, $expected ) );
	}

	/**
	 * @covers ::get
	 * @covers ::query
	 * @covers ::body
	 * @covers ::cookies
	 * @covers ::files
	 * @covers ::server
	 * @covers ::headers
	 */
	public function testGet_NoKey_ReturnAll() {
		$expected = ['foo' => 'bar'];

		$subject = (new Request( 'GET', '' ))->withAttribute( 'foo', 'bar' );
		$this->assertEquals( $expected, $subject->attributes() );

		$subject = (new Request( 'GET', '' ))->withQueryParams( $expected );
		$this->assertEquals( $expected, $subject->query() );

		$subject = (new Request( 'POST', '' ))->withParsedBody( $expected );
		$this->assertEquals( $expected, $subject->body() );

		$subject = (new Request( 'GET', '' ))->withCookieParams( $expected );
		$this->assertEquals( $expected, $subject->cookies() );

		$subject = (new Request( 'POST', '' ))->withUploadedFiles( $expected );
		$this->assertEquals( $expected, $subject->files() );

		$subject = new Request( 'GET', '', [], null, '1.1', $expected );
		$this->assertEquals( $expected, $subject->server() );

		$subject = new Request( 'GET', '', ['foo' => ['bar']] );
		$this->assertEquals( ['foo' => ['bar']], $subject->headers() );
	}
}
