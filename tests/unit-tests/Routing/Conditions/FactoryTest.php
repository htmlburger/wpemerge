<?php

namespace ObsidianTests\Routing\Conditions;

use Obsidian\Request;
use Obsidian\Routing\Conditions\Custom;
use Obsidian\Routing\Conditions\Factory;
use Obsidian\Routing\Conditions\Multiple;
use Obsidian\Routing\Conditions\PostId;
use Obsidian\Routing\Conditions\Url;
use stdClass;
use WP_UnitTestCase;

/**
 * @coversDefaultClass \Obsidian\Routing\Conditions\Factory
 */
class FactoryTest extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();

        $this->request = new Request( [], [], [], [], [], [] );
    }

    public function tearDown() {
        parent::tearDown();

        $this->request = null;
    }

    /**
     * @covers ::make
     * @covers ::makeFromUrl
     */
    public function testMake_Url_UrlCondition() {
        $expected_param = '/foo/bar/';
        $expected_class = Url::class;

        $condition = Factory::make( $expected_param );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertEquals( $expected_param, $condition->getUrl() );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @covers ::getConditionTypeAndArguments
     * @covers ::conditionTypeRegistered
     */
    public function testMake_ConditionInArray_ConditionInstance() {
        $expected_param = 10;
        $expected_class = PostId::class;

        $condition = Factory::make( ['post_id', $expected_param] );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertEquals( $expected_param, $condition->getArguments( $this->request )[0] );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @covers ::getConditionTypeAndArguments
     * @covers ::conditionTypeRegistered
     */
    public function testMake_CustomConditionWithClosureInArray_CustonCondition() {
        $expected_param = function() {};
        $expected_class = Custom::class;

        $condition = Factory::make( ['custom', $expected_param] );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertSame( $expected_param, $condition->getCallable() );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @covers ::getConditionTypeAndArguments
     * @covers ::conditionTypeRegistered
     */
    public function testMake_CustomConditionWithCallableInArray_CustomCondition() {
        $expected_param = 'phpinfo';
        $expected_class = Custom::class;

        $condition = Factory::make( ['custom', $expected_param] );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertSame( $expected_param, $condition->getCallable() );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @covers ::getConditionTypeAndArguments
     * @covers ::conditionTypeRegistered
     */
    public function testMake_ClosureInArray_CustomCondition() {
        $expected_param = function() {};
        $expected_class = Custom::class;

        $condition = Factory::make( [$expected_param] );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertSame( $expected_param, $condition->getCallable() );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @covers ::getConditionTypeAndArguments
     * @covers ::conditionTypeRegistered
     */
    public function testMake_CallableInArray_CustomCondition() {
        $expected_param = 'phpinfo';
        $expected_class = Custom::class;

        $condition = Factory::make( [$expected_param] );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertSame( $expected_param, $condition->getCallable() );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @covers ::makeFromArrayOfConditions
     */
    public function testMake_ArrayOfConditionsInArray_MultipleCondition() {
        $expected_param1 = function() {};
        $expected_param2 = function() {};
        $expected_class = Multiple::class;

        $condition = Factory::make( [ [ $expected_param1 ], [ $expected_param2 ] ] );
        $this->assertInstanceOf( $expected_class, $condition );

        $condition_conditions = $condition->getConditions();
        $this->assertSame( $expected_param1, $condition_conditions[0]->getCallable() );
        $this->assertSame( $expected_param2, $condition_conditions[1]->getCallable() );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @covers ::getConditionTypeAndArguments
     * @covers ::conditionTypeRegistered
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown condition
     */
    public function testMake_UnknownConditionType_Exception() {
        $expected_param = 'foobar';

        $condition = Factory::make( [ $expected_param ] );
    }

    /**
     * @covers ::make
     * @covers ::makeFromArray
     * @expectedException \Exception
     * @expectedExceptionMessage No condition type
     */
    public function testMake_NoConditionType_Exception() {
        $condition = Factory::make( [] );
    }

    /**
     * @covers ::make
     * @covers ::makeFromClosure
     */
    public function testMake_Closure_CustomCondition() {
        $expected_param = function() {};
        $expected_class = Custom::class;

        $condition = Factory::make( $expected_param );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertSame( $expected_param, $condition->getCallable() );
    }

    /**
     * @covers ::make
     */
    public function testMake_Callable_UrlCondition() {
        $expected_param = 'phpinfo';
        $expected_class = Url::class;

        $condition = Factory::make( $expected_param );
        $this->assertInstanceOf( $expected_class, $condition );
        $this->assertEquals( '/' . $expected_param . '/', $condition->getUrl() );
    }

    /**
     * @covers ::make
     * @expectedException \Obsidian\Routing\Conditions\InvalidRouteConditionException
     * @expectedExceptionMessage Invalid condition options
     */
    public function testMake_Object_Exception() {
        $expected_param = new stdClass();

        $condition = Factory::make( $expected_param );
    }
}
