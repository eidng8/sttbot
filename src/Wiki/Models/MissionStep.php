<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-10
 * Time: 22:52
 */

namespace eidng8\Wiki\Models;

/**
 * Mission step model
 */
class MissionStep extends Model
{
    /**
     * @var ReqAndBonus[]
     */
    public $skills;

    /**
     * @var ReqAndBonus[]
     */
    public $traits;

    /**
     * @var string[]
     */
    public $locks;

    /**
     * @var CrewMember[]
     */
    protected $crew;

    /**
     * @return bool
     */
    public function validate(): bool
    {
        // check skill requirements
        if (empty($this->skills)) {
            return false;
        }

        foreach ($this->skills as $skill) {
            if (!$skill->validate()) {
                return false;
            }
        }//end foreach

        return true;
    }//end validate()

    /**
     * Converts the instance to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $skills = [];
        foreach ($this->skills as $skill) {
            $skills[] = $skill->toArray();
        }//end foreach

        $traits = [];
        if (!empty($this->traits)) {
            foreach ($this->traits as $trait) {
                $traits[] = empty($trait) ? [] : $trait->toArray();
            }//end foreach
        }

        return [
            'skills' => $skills,
            'traits' => $traits,
            'locks'  => $this->locks,
        ];
    }

    /**
     * @return string[]
     */
    public function allSkills(): array
    {
        $skills = [];
        foreach ($this->skills as $skill) {
            foreach ($skill->names() as $name) {
                $skills[] = Skills::skillName($name);
            }//end foreach
        }//end foreach

        return $skills;
    }//end allSkills()

    /**
     * Returns all bonus traits
     *
     * @return array
     */
    public function allTraits(): array
    {
        if (empty($this->traits)) {
            return [];
        }

        $traits = [];
        foreach ($this->traits as $trait) {
            $traits = array_merge($traits, $trait['names']);
        }//end foreach
        return $traits;
    }//end allSkills()

    /**
     * Finds the max bonus
     *
     * @return int
     */
    public function maxBonus(): int
    {
        if (empty($this->traits)) {
            return 0;
        }

        return max($this->traits[0]->get());
    }//end maxBonus()

    /**
     * @return CrewMember[]
     */
    public function getCrew(): array
    {
        return $this->crew;
    }

    /**
     * @param array $crew
     */
    public function setCrew(array $crew)
    {
        $this->crew = $crew;
    }

    /**
     * @return array
     */
    public function getPassCrew(): array
    {
        return $this->crew['pass'];
    }

    /**
     * @return array
     */
    public function getCriticalCrew(): array
    {
        return $this->crew['critical'];
    }

    /**
     * @return array
     */
    public function getUnlockCrew(): array
    {
        return $this->crew['unlock'];
    }

    /**
     * @param CrewMember $crew
     */
    public function addPassCrew($crew)
    {
        $this->crew['pass'][] = $crew;
    }

    /**
     * @param CrewMember $crew
     */
    public function addCriticalCrew($crew)
    {
        $this->crew['critical'][] = $crew;
    }

    /**
     * @param CrewMember $crew
     */
    public function addUnlockCrew($crew)
    {
        $this->crew['unlock'][] = $crew;
    }
}//end class
