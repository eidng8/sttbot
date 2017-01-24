<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-20
 * Time: 10:30
 */

namespace eidng8\Wiki\Models;

use eidng8\Wiki\Models\Mission as MissionModel;

/**
 * A data model representing values that are specific to difficulty levels
 */
class LeveledValues extends Model
{
    /**
     * @var int[]
     */
    protected $values = [];

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->values;
    }//end toArray()

    /**
     * Get specific difficulty level value
     *
     * @return int[]
     */
    public function get(): array
    {
        return $this->values;
    }//end get()

    /**
     * Set specific difficulty level value
     *
     * @param int[] $values
     */
    public function set(array $values): void
    {
        $this->values = $values;
    }//end set()

    /**
     * Get or set normal level value
     *
     * @param int|null $value omit to get the value
     *
     * @return int
     */
    public function normal(int $value = null): int
    {
        if (is_numeric($value)) {
            $this->values[MissionModel::NORMAL] = $value;
        }

        return $this->values[MissionModel::NORMAL];
    }//end normal()

    /**
     * Get or set elite level value
     *
     * @param int|null $value omit to get the value
     *
     * @return int
     */
    public function elite(int $value = null): int
    {
        if (is_numeric($value)) {
            $this->values[MissionModel::ELITE] = $value;
        }

        return $this->values[MissionModel::ELITE];
    }//end elite()

    /**
     * Get or set epic level value
     *
     * @param int|null $value omit to get the value
     *
     * @return int
     */
    public function epic(int $value = null): int
    {
        if (is_numeric($value)) {
            $this->values[MissionModel::EPIC] = $value;
        }

        return $this->values[MissionModel::EPIC];
    }//end epic()
}//end class
