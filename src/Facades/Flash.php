<?php

namespace CarbonFramework\Facades;

class Flash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.flash.flash';
    }
}
