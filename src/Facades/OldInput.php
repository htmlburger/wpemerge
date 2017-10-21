<?php

namespace CarbonFramework\Facades;

class OldInput extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.old_input.old_input';
    }
}
