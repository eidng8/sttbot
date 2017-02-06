<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-13
 * Time: 22:09
 */

namespace eidng8;

use eidng8\Wiki\Analyst;
use eidng8\Wiki\Models\Mission;
use eidng8\Wiki\Templates\CrewList;
use eidng8\Wiki\Templates\MissionList;
use eidng8\Wiki\WikiBase;

define('VERSION', 1);

/**
 * Wiki service class
 */
class Wiki extends WikiBase
{
    /**
     * Crew list
     *
     * @var CrewList
     */
    protected $crew;

    /**
     * MissionList instance
     *
     * @var MissionList
     */
    protected $missions;

    /**
     * Analyst instance
     *
     * @var Analyst
     */
    protected $analyst;

    public function analyse(): Analyst
    {
        if (!($this->analyst instanceof Analyst)) {
            $this->analyst = new Analyst($this->missions(), $this->crew());
            $this->analyst->computeCrossRating();
            $this->analyst->computeBestCrew();
        }

        return $this->analyst;
    }//end analyst()

    /**
     * get Crew list
     *
     * @return CrewList
     */
    public function crew(): CrewList
    {
        if ($this->crew) {
            return $this->crew;
        }

        $this->parse->resetOptions();
        $this->parse->page('Crew', 9);
        $this->parse->get(true);
        $tableText = $this->parse->table(0);

        return $this->crew
            = new CrewList($tableText, $this->parse, $this->query);
    }//end crew()

    /**
     * get Mission list
     *
     * @return MissionList
     */
    public function missions(): MissionList
    {
        if ($this->missions) {
            return $this->missions;
        }

        $this->missions = new MissionList(
            $this->parse,
            $this->query,
            $this->expandTemplates
        );

        $this->missions->fetch();

        return $this->missions;
    }//end missions()

    /**
     * Export all data
     *
     * @return array
     */
    public function export(): array
    {
        $export = [
            'version'  => time(),
            'missions' => $this->analyst->getMissions()->export(),
            'crew'     => $this->analyst->getCrew()->export(),
        ];

        $missionIndices = $this->missionIndex($export);
        $crewIndices = $this->crewIndex($export);
        $this->missionStats(
            $this->analyst->getMissions(),
            $missionIndices,
            $crewIndices,
            $export
        );

        return $export;
    }//end export()

    /**
     * @param MissionList $missions
     * @param array       $missionIndices
     * @param array       $crewIndices
     * @param array       $data
     */
    private function missionStats(
        MissionList $missions,
        array $missionIndices,
        array $crewIndices,
        array &$data
    ): void {
        $missions->each(function (Mission $mission) use (
            &$data,
            $missionIndices,
            $crewIndices
        ) {
            if (empty($mission->steps)) {
                return;
            }
            $midx = $missionIndices[$mission->name];
            foreach ($mission->steps as $idx => $step) {
                if (!empty($step['crew']['critical'])) {
                    foreach ($step['crew']['critical'] as $member) {
                        $data['missions'][1][$midx]['steps'][$idx]['crew']['critical'][]
                            = $crewIndices[$member->name];
                    }//end foreach
                }
                if (!empty($step['crew']['pass'])) {
                    foreach ($step['crew']['pass'] as $member) {
                        $data['missions'][1][$midx]['steps'][$idx]['crew']['pass'][]
                            = $crewIndices[$member->name];
                    }//end foreach
                }
                if (!empty($step['crew']['unlock'])) {
                    foreach ($step['crew']['unlock'] as $member) {
                        $data['missions'][1][$midx]['steps'][$idx]['crew']['unlock'][]
                            = $crewIndices[$member->name];
                    }//end foreach
                }
            }//end foreach
        });
    }//end missionStats()

    /**
     * @param array $data
     *
     * @return array
     */
    private function missionIndex(array $data): array
    {
        $indices = [];
        foreach ($data['missions'][1] as $idx => $mission) {
            $indices[$mission['name']] = $idx;
        }//end foreach
        return $indices;
    }//end missionIndex()

    /**
     * @param array $data
     *
     * @return array
     */
    private function crewIndex(array $data): array
    {
        $indices = [];
        foreach ($data['crew'] as $idx => $member) {
            $indices[$member['name']] = $idx;
        }//end foreach
        return $indices;
    }//end crewIndex()
}//end class
