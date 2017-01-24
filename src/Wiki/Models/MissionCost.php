<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-20
 * Time: 10:30
 */

namespace eidng8\Wiki\Models;

/**
 * Mission cost data model
 */
class MissionCost extends LeveledValues
{
    protected $chroniton = false;

    protected $ticket = false;

    /* @noinspection PhpInconsistentReturnPointsInspection */
    /**
     * Get or set ticket cost
     *
     * @param int|null $cost omit to get the cost
     *
     * @return int
     */
    public function ticket(int $cost = null): ?int
    {
        if (!is_numeric($cost)) {
            return $this->values[0];
        }

        $this->values = [$cost];

        return $this->values[0];
    }//end ticket()

    /* @noinspection PhpInconsistentReturnPointsInspection */
    /**
     * If the mission uses chroniton
     *
     * @param bool|null $use omit to get the current value
     *
     * @return bool
     */
    public function useChroniton(bool $use = null): ?bool
    {
        if (null === $use) {
            return $this->chroniton;
        }

        return $this->chroniton = $use;
    }//end useChoniton()

    /* @noinspection PhpInconsistentReturnPointsInspection */
    /**
     * If the mission uses ticket
     *
     * @param bool|null $use omit to get the current value
     *
     * @return bool
     */
    public function useTicket(bool $use = null): ?bool
    {
        if (null === $use) {
            return $this->ticket;
        }

        return $this->ticket = $use;
    }//end useTicket()

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->values;
    }//end toArray()
}//end class
