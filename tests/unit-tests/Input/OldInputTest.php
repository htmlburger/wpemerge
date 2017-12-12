<?php

namespace WPEmergeTests\Input;

use Mockery;
use WPEmerge;
use WPEmerge\Input\OldInput;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \WPEmerge\Input\OldInput
 */
class OldInputTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->flashMock = Mockery::mock()->shouldIgnoreMissing()->asUndefined();

		WPEmerge::facade( 'Flash', OldInputTestFlashFacade::class );
		$container = WPEmerge::getContainer();
		$container['flashMock'] = $this->flashMock;
		$this->subject = new OldInput();
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();

		$container = WPEmerge::getContainer();
		unset( $container['flashMock'] );
		unset( $this->flashMock );
		unset( $this->subject );
		\Flash::clearResolvedInstances();
	}

	/**
	 * @covers ::all
	 * @covers ::store
	 */
	public function testAll() {
		$expected = ['foo' => 'bar', 'bar'=>'baz'];

		$this->flashMock->shouldReceive( 'add' )
			->with( OldInput::FLASH_KEY, $expected )
			->ordered();

		$this->flashMock->shouldReceive( 'peek' )
			->with( OldInput::FLASH_KEY )
			->andReturn( $expected )
			->ordered();

		$this->subject->store( $expected );
		$this->assertEquals( $expected, $this->subject->all() );
	}

	/**
	 * @covers ::all
	 * @covers ::clear
	 */
	public function testClear() {
		$expected = [];

		$this->flashMock->shouldReceive( 'clear' )
			->with( OldInput::FLASH_KEY )
			->ordered();

		$this->flashMock->shouldReceive( 'peek' )
			->with( OldInput::FLASH_KEY )
			->andReturn( $expected )
			->ordered();

		$this->subject->clear();
		$this->assertEquals( $expected, $this->subject->all() );
	}

	/**
	 * @covers ::get
	 */
	public function testGet_ExistingKey_ReturnValue() {
		$data = ['foo' => 'bar', 'bar'=>'baz'];
		$key = 'bar';
		$expected = $data[ $key ];

		$this->flashMock->shouldReceive( 'peek' )
			->with( OldInput::FLASH_KEY )
			->andReturn( $data );

		$this->assertEquals( $expected, $this->subject->get( $key ) );
	}

	/**
	 * @covers ::get
	 */
	public function testGet_NonexistantKey_ReturnDefault() {
		$key = 'nonexistantKey';
		$expected = 'foobar';

		$this->flashMock->shouldReceive( 'peek' )
			->with( OldInput::FLASH_KEY, $key, $expected )
			->andReturn( $expected );

		$this->assertEquals( $expected, $this->subject->get( $key, $expected ) );
	}
}

class OldInputTestFlashFacade extends WPEmerge\Support\Facade {
	protected static function getFacadeAccessor() {
        return 'flashMock';
    }
}
