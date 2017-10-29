<?php

namespace CarbonFrameworkTestTools;

class TestServiceFacade extends \CarbonFramework\Support\Facade {
    protected static function getFacadeAccessor() {
        return 'test_service';
    }
}
