<?php

namespace ObsidianTestTools;

class TestServiceFacade extends \Obsidian\Support\Facade {
    protected static function getFacadeAccessor() {
        return 'test_service';
    }
}
