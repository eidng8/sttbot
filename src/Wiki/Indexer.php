<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2017-02-06
 * Time: 17:02
 */

namespace eidng8\Wiki;

use eidng8\Wiki\Models\Skills;

class Indexer
{

    /**
     * @var array
     */
    public $missionIndex;

    /**
     * @var array
     */
    public $crewIndex;

    /**
     * @var array
     */
    public $skillIndex;

    /**
     * @var array
     */
    public $charIndex;

    /**
     * @var array
     */
    public $raceIndex;

    /**
     * @var array
     */
    public $traitIndex;


    public function __construct()
    {
        $this->loadSkills();
    }//end __construct()

    /**
     * @param array $data
     *
     * @return array
     */
    public function loadMissions(array $data): array
    {
        $this->missionIndex = [];
        foreach ($data as $idx => $mission) {
            $this->missionIndex[$mission['name']] = $idx;
        }//end foreach
        return $this->missionIndex;
    }//end loadMissions()

    /**
     * @param array $data
     *
     * @return array
     */
    public function loadCrew(array $data): array
    {
        $this->crewIndex = [];
        $chars = [];
        $races = [];
        $traits = [];

        foreach ($data as $idx => $member) {
            $this->crewIndex[$member['name']] = $idx;
            $chars[] = $this->getCharacter($member);
            $races[] = $this->getRace($member);
            $traits = array_merge($traits, $this->getTraits($member));
        }//end foreach

        $chars = array_unique($chars);
        sort($chars);
        $this->buildCharacterIndex($chars);

        $races = array_unique($races);
        sort($races);
        $this->buildRaceIndex($races);

        $traits = array_unique($traits);
        sort($traits);
        $this->buildTraitIndex($traits);

        return $this->crewIndex;
    }//end loadCrew()

    /**
     * Get character index by name
     *
     * @param $name
     *
     * @return int
     */
    public function character($name): int
    {
        return $this->charIndex[$name] ?? -1;
    }//end character()

    /**
     * Returns list of all characters
     *
     * @return array
     */
    public function characters(): array
    {
        return array_keys($this->charIndex);
    }//end characters()

    /**
     * Get crew index by name
     *
     * @param $name
     *
     * @return int
     */
    public function crew($name): int
    {
        return $this->crewIndex[$name] ?? -1;
    }//end character()

    /**
     * Get missions index by name
     *
     * @param $name
     *
     * @return int
     */
    public function mission($name): int
    {
        return $this->missionIndex[$name] ?? -1;
    }//end mission()

    /**
     * Get race index by name
     *
     * @param $name
     *
     * @return int
     */
    public function race($name): int
    {
        return $this->raceIndex[$name] ?? -1;
    }//end race()

    /**
     * Returns list of all races
     *
     * @return array
     */
    public function races(): array
    {
        return array_keys($this->raceIndex);
    }//end races()

    /**
     * Get skill index by abbr
     *
     * @param $abbr
     *
     * @return int
     */
    public function skill($abbr): int
    {
        return $this->skillIndex[$abbr] ?? -1;
    }//end skill()

    /**
     * Returns list of all skills
     *
     * @return array
     */
    public function skills(): array
    {
        return array_keys($this->skillIndex);
    }//end races()

    /**
     * Get trait index by name
     *
     * @param $name
     *
     * @return int
     */
    public function trait($name): int
    {
        return $this->traitIndex[$name] ?? -1;
    }//end trait()

    /**
     * Returns list of all traits
     *
     * @return array
     */
    public function traits(): array
    {
        return array_keys($this->traitIndex);
    }//end traits()

    /**
     * Returns the character name of the given crew member
     *
     * @param array $member
     *
     * @return string
     */
    private function getCharacter(array $member): string
    {
        return $member['character'];
    }//end loadCharacters()

    /**
     * Returns the race of the given crew member
     *
     * @param array $member
     *
     * @return string
     */
    private function getRace(array $member): string
    {
        return $member['race'];
    }//end loadRaces()

    /**
     * Loads all available skills
     *
     * @return array
     */
    private function loadSkills(): array
    {
        foreach (Skills::SKILLS as $index => $abbr) {
            $this->skillIndex[$abbr] = $index;
        }
        return $this->skillIndex;
    }//end loadSkills()

    /**
     * Returns the traits of the given crew member
     *
     * @param array $member
     *
     * @return array
     */
    private function getTraits(array $member): array
    {
        return $member['traits'];
    }//end loadTraits()

    /**
     * @param array $chars
     */
    private function buildCharacterIndex(array $chars): void
    {
        $this->charIndex = [];
        foreach ($chars as $index => $char) {
            $this->charIndex[$char] = $index;
        }
    }//end buildCharacterIndex()

    /**
     * @param array $races
     */
    private function buildRaceIndex(array $races): void
    {
        $this->raceIndex = [];
        foreach ($races as $index => $race) {
            $this->raceIndex[$race] = $index;
        }
    }//end buildRaceIndex()

    /**
     * @param array $traits
     */
    private function buildTraitIndex(array $traits): void
    {
        $this->traitIndex = [];
        foreach ($traits as $index => $trait) {
            $this->traitIndex[$trait] = $index;
        }
    }//end buildTraitIndex()
}//end class
