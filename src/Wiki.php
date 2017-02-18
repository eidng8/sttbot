<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-13
 * Time: 22:09
 */

namespace eidng8;

use eidng8\Wiki\Analyst;
use eidng8\Wiki\Exporter;
use eidng8\Wiki\Templates\CrewList;
use eidng8\Wiki\Templates\MissionList;
use eidng8\Wiki\WikiBase;

define('STTBOT_VERSION', 1);

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
        return (new Exporter($this->analyst))->export();
    }//end export()
}//end class
