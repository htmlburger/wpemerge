<?php

namespace WPEmergeTestTools;

class TestServiceFacade extends \WPEmerge\Support\Facade {
    protected static function getFacadeAccessor() {
        return 'test_service';
    }
}
