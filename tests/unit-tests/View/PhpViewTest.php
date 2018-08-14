<?php

namespace WPEmergeTests\View;

use Mockery;
use WPEmerge\Facades\View;
use WPEmerge\View\PhpView;
use WPEmerge\View\ViewInterface;
use WPEmergeTestTools\Helper;
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
	 * @covers ::getFilepath
	 * @covers ::setFilepath
	 */
	public function testGetFilepath() {
		$expected = 'foo';
		$this->subject->setFilepath( $expected );
		$this->assertEquals( $expected, $this->subject->getFilepath() );
	}

	/**
	 * @covers ::getLayout
	 * @covers ::setLayout
	 */
	public function testGetLayout() {
		$expected = Mockery::mock( ViewInterface::class );
		$this->subject->setLayout( $expected );
		$this->assertSame( $expected, $this->subject->getLayout() );
	}

	/**
	 * @covers ::toString
	 * @covers ::render
	 */
	public function testRender() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view.php';
		$expected = file_get_contents( $view );

		$this->subject->setName( $view );
		$this->subject->setFilepath( $view );
		$this->assertEquals( $expected, $this->subject->toString() );
	}

	/**
	 * @covers ::toString
	 */
	public function testToString_Layout() {
		list( $view, $layout, $expected, $handle ) = Helper::createLayoutView();

		$layout = (new PhpView())
			->setName( $layout )
			->setFilepath( $layout );

		$this->subject
			->setName( $view )
			->setFilepath( $view )
			->setLayout( $layout );

		$this->assertEquals( $expected, trim( $this->subject->toString() ) );

		Helper::deleteLayoutView( $handle );
	}

	/**
	 * @covers ::toString
	 * @expectedException \WPEmerge\Exceptions\ViewException
	 * @expectedExceptionMessage must have a name
	 */
	public function testToString_WithoutName() {
		$this->subject->toString();
	}

	/**
	 * @covers ::toString
	 * @expectedException \WPEmerge\Exceptions\ViewException
	 * @expectedExceptionMessage must have a filepath
	 */
	public function testToString_WithoutFilepath() {
		$this->subject->setName( 'foo' );
		$this->subject->toString();
	}

	/**
	 * @covers ::compose
	 */
	public function testCompose_GlobalContext() {
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
	 * @covers ::compose
	 */
	public function testCompose_ViewComposer() {
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
	 * @covers ::compose
	 */
	public function testCompose_LocalContext() {
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
	 * @covers ::compose
	 */
	public function testCompose_LocalContextOverridesViewComposerContext() {
		$view = WPEMERGE_TEST_DIR . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = 'Hello World!';

		$this->viewMock->shouldReceive( 'getGlobals' )
			->andReturn( [] )
			->once();

		$this->viewMock->shouldReceive( 'compose' )
			->andReturnUsing( function( $view ) {
				$view->with( ['world' => 'This should be overridden'] );
			} )
			->once();

		$this->subject->setName( $view );
		$this->subject->setFilepath( $view );
		$this->subject->with( ['world' => 'World'] );

		$this->assertEquals( $expected, trim( $this->subject->toString() ) );
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
