<?php

namespace WPEmergeTests\Helpers;

use Mockery;
use WPEmerge\Helpers\Url;
use WPEmerge\Requests\RequestInterface;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Helpers\Url
 */
class UrlTest extends TestCase {
	/**
	 * @covers ::getPath
	 */
	public function testGetPath_ExternalUrl_FullPathRegardlessOfHomePath() {
		$expected = '/foo';

		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://external.example.org/foo' );

		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org/foo' ) );
	}

	/**
	 * @covers ::getPath
	 */
	public function testGetPath_Home_Slash() {
		$expected = '/';

		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org' );

		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org' ) );
		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org/' ) );
	}

	/**
	 * @covers ::getPath
	 */
	public function testGetPath_HomeTrailingSlash_Slash() {
		$expected = '/';

		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/' );

		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org' ) );
		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org/' ) );
	}

	/**
	 * @covers ::getPath
	 */
	public function testGetPath_Subdirectory_RelativePath() {
		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/' );

		$this->assertEquals( '/foo/bar', Url::getPath( $request, 'http://example.org/' ) );
		$this->assertEquals( '/bar', Url::getPath( $request, 'http://example.org/foo/' ) );
		$this->assertEquals( '/', Url::getPath( $request, 'http://example.org/foo/bar/' ) );
		$this->assertEquals( '/foo/bar', Url::getPath( $request, 'http://example.org/foo/bar/baz' ) );
		$this->assertEquals( '/foo/bar', Url::getPath( $request, 'http://example.org/foo/baz' ) );
	}

	/**
	 * @covers ::getPath
	 */
	public function testGetPath_DifferentSubdirectory() {
		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/baz' );

		$this->assertEquals( '/baz', Url::getPath( $request, 'http://example.org/foo' ) );
		$this->assertEquals( '/baz', Url::getPath( $request, 'http://example.org/foo/' ) );

		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/baz/' );

		$this->assertEquals( '/baz', Url::getPath( $request, 'http://example.org/foo' ) );
		$this->assertEquals( '/baz', Url::getPath( $request, 'http://example.org/foo/' ) );
	}

	/**
	 * @covers ::getPath
	 */
	public function testGetPath_QueryString_StripsQuery() {
		$expected = '/foo/bar';

		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/?foo=bar' );

		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org/' ) );
	}

	/**
	 * @covers ::getPath
	 */
	public function testGetPath_Anchor_StripsAnchor() {
		$expected = '/foo/bar';

		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/#foo' );

		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org/' ) );
	}

	/**
	 * @covers ::getPath
	 */
	public function testGetPath_QueryAnchor_StripsQueryAnchor() {
		$expected = '/foo/bar';

		$request = Mockery::mock( RequestInterface::class );
		$request->shouldReceive( 'getUrl' )
			->andReturn( 'http://example.org/foo/bar/?foo=bar#foo' );

		$this->assertEquals( $expected, Url::getPath( $request, 'http://example.org/' ) );
	}

	/**
	 * @covers ::addLeadingSlash
	 */
	public function testAddLeadingSlash() {
		$this->assertEquals( '', Url::addLeadingSlash( '', true ) );
		$this->assertEquals( '/', Url::addLeadingSlash( '' ) );
		$this->assertEquals( '/', Url::addLeadingSlash( '/' ) );
		$this->assertEquals( '/example', Url::addLeadingSlash( 'example') );
		$this->assertEquals( '/example', Url::addLeadingSlash( '/example') );
	}

	/**
	 * @covers ::removeLeadingSlash
	 */
	public function testRemoveLeadingSlash() {
		$this->assertEquals( 'example', Url::removeLeadingSlash( '/example') );
		$this->assertEquals( 'example', Url::removeLeadingSlash( 'example') );
	}

	/**
	 * @covers ::addTrailingSlash
	 */
	public function testAddTrailingSlash() {
		$this->assertEquals( '', Url::addTrailingSlash( '', true ) );
		$this->assertEquals( '/', Url::addTrailingSlash( '' ) );
		$this->assertEquals( '/', Url::addTrailingSlash( '/' ) );
		$this->assertEquals( 'example/', Url::addTrailingSlash( 'example') );
		$this->assertEquals( 'example/', Url::addTrailingSlash( 'example/') );
	}

	/**
	 * @covers ::removeTrailingSlash
	 */
	public function testRemoveTrailingSlash() {
		$this->assertEquals( 'example', Url::removeTrailingSlash( 'example/') );
		$this->assertEquals( 'example', Url::removeTrailingSlash( 'example') );
	}
}
