<?php

use CarbonFramework\Flash\Flash;

class FlashTest extends WP_UnitTestCase {
    /**
     * @covers \CarbonFramework\Flash::enabled
     */
    public function testEnabled() {
        $expected1 = false;
        $storage1 = null;
        $subject1 = new Flash( $storage1 );
        $this->assertEquals( $expected1, $subject1->enabled() );

        $expected2 = true;
        $storage2 = [];
        $subject2 = new Flash( $storage2 );
        $this->assertEquals( $expected2, $subject2->enabled() );
    }

    /**
     * @covers \CarbonFramework\Flash::add
     * @covers \CarbonFramework\Flash::peek
     */
    public function testPeek_ExistingKey_ReturnValue() {
        $expected = ['foo'];
        $key = 'key';
        $storage = [];
        
        $subject = new Flash( $storage );
        $subject->add( $key, $expected );

        $this->assertEquals( $expected, $subject->peek( $key ) );
    }

    /**
     * @covers \CarbonFramework\Flash::peek
     */
    public function testPeek_NonExistantKey_ReturnEmptyArray() {
        $expected = [];
        $key = 'key';
        $storage = [];
        
        $subject = new Flash( $storage );

        $this->assertEquals( $expected, $subject->peek( $key ) );
    }

    /**
     * @covers \CarbonFramework\Flash::add
     * @covers \CarbonFramework\Flash::peek
     */
    public function testPeek_StringValue_ReturnValueInArray() {
        $expected_value = 'foo';
        $expected = [$expected_value];
        $key = 'key';
        $storage = [];
        
        $subject = new Flash( $storage );
        $subject->add( $key, $expected_value );

        $this->assertEquals( $expected, $subject->peek( $key ) );
    }

    /**
     * @covers \CarbonFramework\Flash::add
     * @covers \CarbonFramework\Flash::get
     */
    public function testGet_ExistingKey_ReturnValueAndClear() {
        $expected1 = ['foo'];
        $expected2 = [];
        $key = 'key';
        $storage = [];
        
        $subject = new Flash( $storage );
        $subject->add( $key, $expected1 );

        $this->assertEquals( $expected1, $subject->get( $key ) );
        $this->assertEquals( $expected2, $subject->get( $key ) );
    }

    /**
     * @covers \CarbonFramework\Flash::add
     * @covers \CarbonFramework\Flash::peek
     */
    public function testAdd_CalledMultipledTimes_ReturnArrayOfValues() {
        $expected = ['foo', 'bar', 'baz'];
        $key = 'key';
        $storage = [];
        
        $subject = new Flash( $storage );
        $subject->add( $key, $expected[0] );
        $subject->add( $key, $expected[1] );
        $subject->add( $key, $expected[2] );

        $this->assertEquals( $expected, $subject->peek( $key ) );
    }

    /**
     * @covers \CarbonFramework\Flash::add
     * @covers \CarbonFramework\Flash::peek
     */
    public function testAdd_CalledWithDifferentKeys_StoreNestedArray() {
        $expected = ['key1' => ['foo'], 'key2' => ['bar']];
        $values = array_values( $expected );
        $keys = array_keys( $expected );
        $storage = [];
        
        $subject = new Flash( $storage );
        $subject->add( $keys[0], $values[0][0] );
        $subject->add( $keys[1], $values[1][0] );

        $this->assertEquals( $expected, $subject->peek() );
    }

    /**
     * @covers \CarbonFramework\Flash::peek
     * @covers \CarbonFramework\Flash::clear
     */
    public function testClear_WithKey_ClearKey() {
        $expected = ['key1' => [], 'key2'=>['bar']];
        $storage = [];
        
        $subject = new Flash( $storage );
        $subject->add( 'key1', 'foo' );
        $subject->add( 'key2', 'bar' );
        $subject->clear( 'key1' );

        $this->assertEquals( $expected, $subject->peek() );
    }

    /**
     * @covers \CarbonFramework\Flash::peek
     * @covers \CarbonFramework\Flash::clear
     */
    public function testClear_WithoutKey_ClearAll() {
        $expected = ['key1' => [], 'key2'=>[]];
        $storage = [];
        
        $subject = new Flash( $storage );
        $subject->add( 'key1', 'foo' );
        $subject->add( 'key2', 'bar' );
        $subject->clear();

        $this->assertEquals( $expected, $subject->peek() );
    }
}
