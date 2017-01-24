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

/**
 * Model base class
 */
abstract class Model implements ArrayAccess, JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->{strtolower(trim($offset))};
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return property_exists($this, strtolower(trim($offset)));
    }//end offsetExists()

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->{strtolower(trim($offset))} = $value;
    }//end offsetGet()

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->{strtolower(trim($offset))} = null;
    }//end offsetSet()

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }//end offsetUnset()

    /**
     * Converts the instance to array
     *
     * @return array
     */
    abstract public function toArray(): array;
}//end class
