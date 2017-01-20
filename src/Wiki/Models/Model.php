<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 17:48
 */

namespace eidng8\Wiki\Models;

use ArrayAccess;
use JsonSerializable;

abstract class Model implements ArrayAccess, JsonSerializable
{

    /**
     * Validate the instance
     *
     * @return bool
     */
    // abstract public function validate(): bool;

    /**
     * Converts the instance to array
     *
     * @return array
     */
    abstract public function toArray(): array;


    public function offsetExists($offset): bool
    {
        return property_exists($this, strtolower(trim($offset)));
    }//end offsetExists()


    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->{strtolower(trim($offset))};
        }

        return null;
    }//end offsetGet()


    public function offsetSet($offset, $value)
    {
        $this->{strtolower(trim($offset))} = $value;
    }//end offsetSet()


    public function offsetUnset($offset)
    {
        $this->{strtolower(trim($offset))} = null;
    }//end offsetUnset()


    public function jsonSerialize(): array
    {
        return $this->toArray();
    }//end jsonSerialize()
}//end class
