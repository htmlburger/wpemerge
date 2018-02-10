<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\Facades\View;
use WPEmerge\View\PhpView;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\View\PhpView
 */
class PhpViewTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->view = View::getFacadeRoot();
		$this->viewMock = Mockery::mock()->shouldIgnoreMissing()->asUndefined();
		View::swap( $this->viewMock );

		$this->subject = new PhpView();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		View::swap( $this->view );
		unset( $this->view );
		unset( $this->viewMock );

		unset( $this->subject );
	}

	/**
	 * @covers ::getName
	 * @covers ::setName
	 */
	public function testGetName() {
		$expected = 'foo';
		$this->subject->setName( $expected );
		$this->assertEquals( $expected, $this->subject->getName() );
	}

	/**
	 * @covers ::getFilepath
	 * @covers ::setFilepath
	 */
	public function testGetFilepath() {
		$expected = 'foo';
		$this->subject->setFilepath( $expected );
		$this->assertEquals( $expected, $this->subject->getFilepath() );
	}

	/**
	 * @covers ::toString
	 * @covers ::render
	 */
	public function testToString_GlobalContext() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-global-context.php';
		$expected = 'Hello World!';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( ['world' => 'World'] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->once();

		$this->subject->setName( $view );
		$this->subject->setFilepath( $view );

		$this->assertEquals( $expected, trim( $this->subject->toString() ) );
	}

	/**
	 * @covers ::toString
	 * @covers ::render
	 */
	public function testToString_ViewComposer() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->andReturnUsing( function( $view ) {
				$view->with( ['world' => 'World'] );
			} )
			->once();

		$this->subject->setName( $view );
		$this->subject->setFilepath( $view );

		$this->assertEquals( $expected, trim( $this->subject->toString() ) );
	}

	/**
	 * @covers ::toString
	 * @covers ::render
	 */
	public function testToString_LocalContext() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->once();

		$this->subject->setName( $view );
		$this->subject->setFilepath( $view );
		$this->subject->with( ['world' => 'World'] );

		$this->assertEquals( $expected, trim( $this->subject->toString() ) );
	}

	/**
	 * @covers ::toString
	 * @covers ::render
	 */
	public function testToString_LocalContextOverridesViewComposerContext() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->andReturnUsing( function( $view ) {
				$view->with( ['world' => 'This should be overriden'] );
			} )
			->once();

		$this->subject->setName( $view );
		$this->subject->setFilepath( $view );
		$this->subject->with( ['world' => 'World'] );

		$this->assertEquals( $expected, trim( $this->subject->toString() ) );
	}

	/**
	 * @covers ::toString
	 * @expectedException \Exception
	 * @expectedExceptionMessage must have a name
	 */
	public function testToString_WithoutName() {
		$this->subject->toString();
	}

	/**
	 * @covers ::toString
	 * @expectedException \Exception
	 * @expectedExceptionMessage must have a filepath
	 */
	public function testToString_WithoutFilepath() {
		$this->subject->setName( 'foo' );
		$this->subject->toString();
	}

	/**
	 * @covers ::toResponse
	 */
	public function testToResponse() {
		$expected = 'foobar';

		$mock = Mockery::mock( PhpView::class )->makePartial();
		$mock->shouldReceive( 'toString' )
			->andReturn( $expected );

		$result = $mock->toResponse();
		$this->assertEquals( 'text/html', $result->getHeaderLine( 'Content-Type' ) );
		$this->assertEquals( $expected, $result->getBody()->read( strlen( $expected ) ) );
	}
}
