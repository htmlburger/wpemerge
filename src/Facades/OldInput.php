<?php

namespace CarbonFramework\Facades;

/**
 * Provide access to old input service
 *
 * @codeCoverageIgnore
 */
class OldInput extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'framework.old_input.old_input';
    }
}
