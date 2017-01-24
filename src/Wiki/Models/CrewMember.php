<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 17:42
 */

namespace eidng8\Wiki\Models;

use eidng8\Contracts\Hyperlink;

/**
 * CrewMember Model
 */
class CrewMember extends Model implements Hyperlink
{
    /**
     * Member name, also used in generating URI & page
     *
     * @var string
     */
    public $name;

    /**
     * Wiki page URI
     *
     * @var string
     */
    public $page;

    /**
     * Member portrait
     *
     * @var string
     */
    public $picture;

    /**
     * Rarity, number of stars
     *
     * @var int
     */
    public $stars;

    /**
     * Character name that the member originated
     *
     * @var string
     */
    public $character;

    /**
     * Character page URI
     *
     * @var string
     */
    public $charpage;

    /**
     * @var string
     */
    public $race;

    /**
     * Skills the member possesses
     *
     * @var Skills
     */
    public $skills;

    /**
     * Traits the member possesses
     *
     * @var string[]
     */
    public $traits = [];

    /**
     * Cross rating
     *
     * @var int
     */
    protected $rating = 0;

    /**
     * Missions a crew can pass or critical
     *
     * @var Mission[][]
     */
    protected $missions = ['pass' => [], 'critical' => [], 'unlock' => []];

    /**
     * CrewMember constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if (empty($data)) {
            $this->skills = new Skills();

            return;
        }

        foreach ($data as $key => $value) {
            if ('skills' == $key) {
                $this['skills'] = new Skills($value);
            } else {
                $this[$key] = $value;
            }
        }//end foreach
    }//end __construct()

    /**
     * Returns the thumbnail URI
     *
     * @return string
     */
    public function thumbnail(): string
    {
        return $this->picture;
    }//end thumbnail()

    /**
     * Checks if crew member can unlock the given mission step
     *
     * @param MissionStep $step
     *
     * @return bool
     */
    public function canUnlock(MissionStep $step): bool
    {
        foreach ($step->locks as $lock) {
            if (empty($lock)) {
                continue;
            }
            $unlock = $this->hasTraits($lock);
            if ($unlock) {
                return $unlock;
            }
        }//end foreach

        return false;
    }//end hasSkill()

    /**
     * Checks if crew member possess the given trait or traits
     *
     * @param string[] $traits
     *
     * @return bool
     */
    public function hasTraits(?array $traits): bool
    {
        if (!empty($traits)) {
            return !empty(array_intersect($traits, $this->traits));
        }

        return false;
    }//end hasSkill()

    /**
     * Checks if crew member can pass the given cadet mission step.
     * "pass" means 100% success (greed color box).
     *
     * @param MissionStep $step
     * @param bool        $critical
     *
     * @return bool
     */
    public function canPassCadet(MissionStep $step, bool $critical = false)
    {
        return $this->eligible($step) && $this->canPass($step, $critical);
    }//end traitBonus()

    /**
     * Check if the crew is eligible to the given cadet mission
     *
     * @param MissionStep $step
     *
     * @return bool
     */
    public function eligible(MissionStep $step): bool
    {
        return empty($step['eligible'])
            ? false : in_array($this->name, $step['eligible']);
    }//end canPass()

    /**
     * Checks if crew member can pass the given mission step.
     * `pass` means probably success (yellow color box).
     * `critical` means 100% critical rate (greed color box).
     *
     * @param MissionStep $step
     * @param bool        $critical
     *
     * @return bool
     */
    public function canPass(MissionStep $step, bool $critical = false): bool
    {
        foreach ($step->skills as $idx => $skill) {
            $name = $skill->name()[0];
            if (!$this->hasSkill($name)) {
                continue;
            }

            $value = $this->skills[$name];

            // use the lower value for critical coz we need 100% critical rate
            // use the higher value otherwise coz we just need probable
            //$value = $critical ? $value[0] : $value[1];

            // The above 100% critical rate seems not feasible,
            // it is impossible to achieve for many missions,
            // especially in Cadet Challenges.
            // Now go for the higher value, to "possible" critical success
            $value = $value[1];

            return $value + $this->traitBonus($step, $idx)
                   >= $skill->epic() * ($critical ? 1.25 : 1);
        }//end foreach

        return false;
    }//end canCritical()

    /**
     * Checks if crew member possess the given skill
     *
     * @param string $skill
     *
     * @return bool
     */
    public function hasSkill(string $skill): bool
    {
        return (bool)$this->skills[$skill];
    }//end canUnlock()

    /**
     * Calculate trait bonus
     *
     * @param MissionStep $step
     * @param int         $idx
     *
     * @return int
     */
    public function traitBonus(MissionStep $step, int $idx)
    {
        if (empty($step->traits) || empty($step->traits[$idx])) {
            return 0;
        }
        $value = 0;
        if ($this->hasTraits($step->traits[$idx]['names'])) {
            $value += max($step->traits[$idx]['values']);
        }

        return $value;
    }//end eligible()

    /**
     * Checks if crew member can "critical success" the given cadet mission step
     *
     * @param MissionStep $step
     *
     * @return bool
     */
    public function canCriticalCadet(MissionStep $step)
    {
        return $this->eligible($step) && $this->canCritical($step);
    }//end canPass()

    /**
     * Checks if crew member can "critical success" the given mission step
     *
     * @param MissionStep $step
     *
     * @return bool
     */
    public function canCritical(MissionStep $step): bool
    {
        return $this->canPass($step, true);
    }//end canCritical()

    /**
     * Checks if crew member can unlock the given cadet mission step
     * As of 2016-12-18, there is no locked cadet mission steps
     *
     * @param MissionStep $step
     *
     * @return bool
     */
    // public function canUnlockCadet(MissionStep $step)
    // {
    //     return $this->eligible($step) && $this->canUnlock($step);
    // }//end canUnlock()

    /**
     * {@inheritdoc}
     */
    public function uri(): string
    {
        return $this->page();
    }//end uri()

    /**
     * {@inheritdoc}
     */
    public function page(): string
    {
        return $this->page;
    }//end page()

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'character' => $this->character,
            'charpage'  => $this->charpage,
            'name'      => $this->name,
            'page'      => $this->page,
            'picture'   => $this->picture,
            'race'      => $this->race,
            'skills'    => $this->skills->toArray(),
            'stars'     => $this->stars,
            'traits'    => $this->traits,
        ];
    }//end toArray()

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        switch ($offset) {
            case 'CrewName':
                return parent::offsetExists('name');
            case 'CharName':
                return parent::offsetExists('character');
        }

        if (Skills::isSkill($offset)) {
            return $this->skills->offsetExists($offset);
        }

        return parent::offsetExists($offset);
    }//end offsetExists()

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'CrewName':
                return parent::offsetGet('name');
            case 'CharName':
                return parent::offsetGet('character');
        }

        if (Skills::isSkill($offset)) {
            return $this->skills->offsetGet($offset);
        }

        return parent::offsetGet($offset);
    }//end offsetGet()

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        switch (strtolower($offset)) {
            case 'crewname':
                list($this->name, $this->page) = $this->parseName($value);
                break;

            case 'charname':
                list(
                    $this->character, $this->charpage
                    )
                    = $this->parseName($value);
                break;

            case 'stars':
                $this->stars = (int)$value;
                break;

            default:
                if (Skills::isSkill($offset)) {
                    $this->skills->offsetSet($offset, $value);
                } else {
                    parent::offsetSet($offset, $value);
                }
        }
    }//end offsetSet()

    /**
     * {@inheritdoc}
     */
    protected function parseName($name): array
    {
        $parts = explode('{{!}}', $name);

        if (1 === count($parts)) {
            return [$parts[0], rawurlencode($parts[0])];
        }

        $parts = array_reverse($parts);
        $parts[1] = rawurlencode($parts[1]);

        return $parts;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        switch ($offset) {
            case 'CrewName':
                $this->name = null;
                break;

            case 'CharName':
                $this->character = null;
                break;

            default:
                if (Skills::isSkill($offset)) {
                    $this->skills->offsetUnset($offset);
                }
                parent::offsetUnset($offset);
        }
    }

    /**
     * Get cross rating
     *
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }//end offsetUnset()

    /**
     * Set cross rating
     *
     * @param int $rating
     */
    public function setRating(int $rating)
    {
        $this->rating = $rating;
    }//end increaseRating()

    /**
     * Increase cross rating
     *
     * @param int $increment
     */
    public function incRating(int $increment = 1)
    {
        $this->rating += $increment;
    }//end addPassMissionStep()

    /**
     * Add the mission step to the crew member's 'pass' {@see $missions} array
     *
     * @param MissionStep $step
     */
    public function addPassMissionStep(MissionStep $step): void
    {
        $this->missions['pass'][] = $step;
    }//end addCriticalMissionStep()

    /**
     * Add the mission step to the crew member's 'critical' {@see $missions}
     * array
     *
     * @param MissionStep $step
     */
    public function addCriticalMissionStep(MissionStep $step): void
    {
        $this->missions['critical'][] = $step;
    }//end addUnlockMissionStep()

    /**
     * Add the mission step to the crew member's 'unlock' {@see $missions} array
     *
     * @param MissionStep $step
     */
    public function addUnlockMissionStep(MissionStep $step): void
    {
        $this->missions['unlock'][] = $step;
    }//end parseName()
}//end class
