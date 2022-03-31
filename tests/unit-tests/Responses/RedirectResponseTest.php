<?php

namespace WPEmergeTests\Responses;

use Mockery;
use WPEmerge\Requests\RequestInterface;
use WPEmerge\Responses\RedirectResponse;
use WPEmergeTestTools\TestCase;

/**
 * @coversDefaultClass \WPEmerge\Responses\RedirectResponse
 */
class RedirectResponseTest extends TestCase {
	public function tear_down() {
		Mockery::close();
	}

	/**
	 * @covers ::to
	 */
	public function testTo_Location() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing()->asUndefined();
		$expected = '/foobar';

		$subject = (new RedirectResponse( $request ))->to( $expected );
		$this->assertEquals( $expected, $subject->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::to
	 */
	public function testTo_Status() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing()->asUndefined();
		$expected1 = 301;
		$expected2 = 302;

		$subject1 = (new RedirectResponse( $request ))->to( 'foobar', $expected1 );
		$this->assertEquals( $expected1, $subject1->getStatusCode() );

		$subject2 = (new RedirectResponse( $request ))->to( 'foobar', $expected2 );
		$this->assertEquals( $expected2, $subject2->getStatusCode() );
	}

	/**
	 * @covers ::back
	 */
	public function testBack_Location() {
		$expected = 'http://example.com/foobar?hello=world';
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getHeaderLine' )
			->with( 'Referer' )
			->andReturn( $expected );

		$subject = (new RedirectResponse( $request ))->back();
		$this->assertEquals( $expected, $subject->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::back
	 */
	public function testBack_Location_Fallback() {
		$expected = 'http://example.com/foobar?hello=world';
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getHeaderLine' )
			->with( 'Referer' )
			->andReturn( null );

		$subject = (new RedirectResponse( $request ))->back( $expected );
		$this->assertEquals( $expected, $subject->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::back
	 */
	public function testBack_Location_Current() {
		$expected = 'http://example.com/foobar?hello=world';
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getHeaderLine' )
			->with( 'Referer' )
			->andReturn( null );

		$request->shouldReceive( 'getUrl' )
			->andReturn( $expected );

		$subject = (new RedirectResponse( $request ))->back();
		$this->assertEquals( $expected, $subject->getHeaderLine( 'Location' ) );
	}

	/**
	 * @covers ::back
	 */
	public function testBack_Status() {
		$expected1 = 301;
		$expected2 = 302;
		$url = 'http://example.com/foobar?hello=world';
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getHeaderLine' )
			->with( 'Referer' )
			->andReturn( $url );

		$subject1 = (new RedirectResponse( $request ))->back( null, $expected1 );
		$this->assertEquals( $expected1, $subject1->getStatusCode() );

		$subject2 = (new RedirectResponse( $request ))->back( null, $expected2 );
		$this->assertEquals( $expected2, $subject2->getStatusCode() );
	}
}
