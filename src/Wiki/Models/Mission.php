<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-17
 * Time: 23:58
 */

namespace eidng8\Wiki\Models;

use eidng8\Contracts\Hyperlink;
use eidng8\Log\Log;

/**
 * Mission model
 */
class Mission extends Model implements Hyperlink
{
    /**
     * Away team mission
     */
    public const AWAY_TEAM = 1;

    /**
     * Elite difficulty
     */
    public const ELITE = 1;

    /**
     * Epic difficulty
     */
    public const EPIC = 2;

    /**
     * Normal difficulty
     */
    public const NORMAL = 0;

    /**
     * Space battle mission
     */
    public const SPACE_BATTLE = 2;

    /**
     * Wiki page URI
     *
     * @var string
     */
    public $page;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $episode;

    /**
     * @var int
     */
    public $index;

    /**
     * @var int
     */
    public $type;

    /**
     * @var MissionCost
     */
    public $cost;

    public $locks;

    /**
     * Bonus traits
     *
     * @var string[]
     */
    public $traits;

    /**
     * Mission steps
     *
     * @var MissionStep[]
     */
    public $steps;

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $result = true;

        // validate cost
        if (empty($this->cost) || !($this->cost instanceof MissionCost)) {
            Log::warn("Mission [ $this->name ] has no cost.");

            return false;
        }

        // currently just check on away team missions
        if (static::AWAY_TEAM === $this->type) {
            // check missions steps
            if (empty($this->steps) || !is_array($this->steps)) {
                Log::warn("Mission [ $this->name ] has no step.", [$this]);

                return false;
            }

            foreach ($this->steps as $idx => $step) {
                if (empty($step) || !($step instanceof MissionStep)) {
                    Log::warn(
                        "Mission [ $this->name ] step ( $idx ) is empty.",
                        [$this]
                    );
                    $result = false;
                } elseif (!$step->validate()) {
                    Log::warn(
                        "Mission [ $this->name ] step ( $idx ) is invalid.",
                        [$this]
                    );
                    $result = false;
                }
            }//end foreach
        }

        return $result;
    }//end validate()

    /**
     * URI of the resource, related to site root, without leading slash
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->page();
    }//end uri()

    /**
     * Page name of the resource
     *
     * @return string
     */
    public function page(): string
    {
        return $this->page;
    }//end page()

    /**
     * Converts the instance to array
     *
     * @return array
     */
    public function toArray(): array
    {
        if (static::SPACE_BATTLE === $this->type) {
            return [
                'name'    => $this->name,
                'page'    => $this->page,
                'episode' => $this->episode,
                'index'   => $this->index,
                'type'    => $this->type,
                'cost'    => $this->cost->toArray(),
            ];
        }

        $steps = [];
        foreach ($this->steps as $step) {
            if (empty($step)) {
                $steps[] = [];
            } else {
                $steps[] = $step->toArray();
            }
        }//end foreach

        return [
            'name'    => $this->name,
            'page'    => $this->page,
            'episode' => $this->episode,
            'index'   => $this->index,
            'type'    => $this->type,
            'cost'    => $this->cost->toArray(),
            'locks'   => $this->locks,
            'traits'  => $this->traits,
            'steps'   => $steps,
        ];
    }//end toArray()
}//end class
