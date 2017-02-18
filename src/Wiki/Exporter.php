<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2017-02-06
 * Time: 17:01
 */

namespace eidng8\Wiki;

class Exporter
{

    /**
     * @var Analyst
     */
    private $analyst;

    /**
     * @var Indexer
     */
    private $indexer;

    /**
     * @param Analyst $analyst
     * @param Indexer $indexer
     */
    public function __construct(Analyst $analyst, Indexer $indexer = null)
    {
        $this->analyst = $analyst;
        $this->indexer = $indexer ?? new Indexer();
    }//end __construct()

    /**
     * Export all data
     *
     * @return array
     */
    public function export(): array
    {
        $crew = $this->analyst->getCrew()->export();
        $missions = $this->analyst->getMissions()->export();
        $this->indexer->loadMissions($missions[1]);
        $this->indexer->loadCrew($crew);

        $export = [
            'version'     => STTBOT_VERSION,
            'generatedAt' => time(),
            'characters'  => $this->indexer->characters(),
            'crew'        => $crew,
            'episodes'    => $missions[0],
            'missions'    => $missions[1],
            'races'       => $this->indexer->races(),
            'skills'      => $this->indexer->skills(),
            'traits'      => $this->indexer->traits(),
        ];

        $export['crew'] = $this->mapCrewAttributes($export['crew']);
        $export['missions'] = $this->mapMissionAttributes($export['missions']);

        return $export;
    }//end export()

    /**
     * Map crew member to indexed values
     *
     * @param array $crew
     *
     * @return array
     */
    private function mapCrew(array $crew): array
    {
        $ret = [];
        foreach ($crew as $member) {
            $ret[] = $this->indexer->crew($member['name']);
        }//end foreach

        return $ret;
    }//end mapCrew()

    /**
     * Map crew attributes to indexed values
     *
     * @param array $crew
     *
     * @return array
     */
    private function mapCrewAttributes(array $crew): array
    {
        $ret = [];
        foreach ($crew as $member) {
            $member['character']
                = $this->indexer->character($member['character']);
            $member['race'] = $this->indexer->race($member['race']);

            $skills = [];
            foreach ($member['skills'] as $abbr => $val) {
                $skills[$this->indexer->skill($abbr)] = $val;
            }
            $member['skills'] = $skills;

            $traits = [];
            foreach ($member['traits'] as $trait) {
                $traits[] = $this->indexer->trait($trait);
            }
            $member['traits'] = $traits;

            $ret[] = $member;
        }//end foreach

        return $ret;
    }//end mapCrewAttributes()

    /**
     * Map mission attributes to indexed values
     *
     * @param array $missions
     *
     * @return array
     */
    private function mapMissionAttributes(array $missions): array
    {
        $ret = [];
        foreach ($missions as $mission) {
            if (empty($mission['steps'])) {
                $ret[] = $mission;
                continue;
            }

            $steps = [];
            foreach ($mission['steps'] as $step) {
                $steps[] = $this->mapStep($step);
            }//end foreach
            $mission['steps'] = $steps;

            $ret[] = $mission;
        }//end foreach

        return $ret;
    }//end mapMissionAttributes()

    /**
     * Map mission step attributes to indexed values
     *
     * @param array $step
     *
     * @return array
     */
    private function mapStep(array $step): array
    {
        $ret = $step;

        $skills = [];
        foreach ($step['skills'] as $skill) {
            $skills[] = $this->indexer->skill($skill);
        }//end foreach
        $ret['skills'] = $skills;

        $ret['traits'] = $this->mapTraits($step['traits']);
        $ret['locks'] = $this->mapTraits($step['locks']);

        if (!empty($step['crew'])) {
            $crew = [];
            foreach ($step['crew'] as $key => $list) {
                $crew[$key] = $this->mapCrew($list);
            }//end foreach
            $ret['crew'] = $crew;
        }

        return $ret;
    }//end mapStep()

    /**
     * Map traits to indexed values
     *
     * @param array $traits
     *
     * @return array
     */
    private function mapTraits(array $traits): array
    {
        $ret = [];
        foreach ($traits as $index => $list) {
            if (empty($list)) {
                $ret[$index] = null;
            } elseif (!is_array($list)) {
                $ret[$index] = $list;
            } else {
                foreach ($list as $trait) {
                    if (empty($trait)) {
                        $ret[$index][] = null;
                        continue;
                    }
                    $val = $this->indexer->trait($trait);
                    if ($val >= 0) {
                        $ret[$index][] = $val;
                    }
                }//end foreach
            }
        }//end foreach

        return $ret;
    }//end mapTraits()
}//end class
