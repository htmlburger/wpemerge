<?php

namespace CarbonFrameworkTestTools;

class TestServiceFacade extends \CarbonFramework\Facades\Facade {
    protected static function getFacadeAccessor() {
        return 'test_service';
    }
}
