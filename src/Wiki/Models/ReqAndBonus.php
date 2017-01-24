<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-10
 * Time: 17:28
 */

namespace eidng8\Wiki\Models;

/**
 * Requirement and bonus values
 */
class ReqAndBonus extends LeveledValues
{
    /**
     * @var string[]
     */
    protected $names;

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (empty($this->names) || !is_array($this->names)) {
            return false;
        }

        if (empty($this->values) || !is_array($this->values)) {
            return false;
        }

        foreach ($this->names as $name) {
            if (empty($name)) {
                return false;
            }
        }//end foreach

        foreach ($this->values as $value) {
            if (empty($value)) {
                return false;
            }
        }//end foreach

        return true;
    }//end validate()

    /**
     * Alias of `name()`
     *
     * @param array|null $names omit to get the value
     *
     * @return string[]
     */
    public function names(array $names = null): array
    {
        return $this->name($names);
    }//end name()

    /**
     * Get or set names
     *
     * @param array|null $names omit to get the value
     *
     * @return string[]
     */
    public function name(array $names = null): array
    {
        if (!is_array($names)) {
            return $this->names;
        }

        return $this->names = $names;
    }//end name()

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'names'  => $this->names,
            'values' => $this->values,
        ];
    }
}//end class
