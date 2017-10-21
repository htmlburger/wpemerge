<?php

// TODO remove

namespace CarbonFramework\Support;

use \Exception;

class StrictFluent extends Fluent
{
    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!array_key_exists($offset, $this->attributes)) {
            throw new \Exception( 'Attempted to set an unknown attribute: ' . $offset );
        }
        parent::offsetSet($offset, $value);
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \Exception( 'Unset is not allowed for StrictFluent objects.' );
    }

    /**
     * Handle dynamic calls to the container to set attributes.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $value = count($parameters) > 0 ? $parameters[0] : true;
        $this->offsetSet($method, $value);
        return $this;
    }
}
