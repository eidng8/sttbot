<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-10
 * Time: 22:06
 */

namespace eidng8\Wiki;

use eidng8\Log\Log;
use eidng8\Wiki\Models\CrewMember;
use eidng8\Wiki\Models\Mission;
use eidng8\Wiki\Models\MissionStep;
use eidng8\Wiki\Templates\CrewList;
use eidng8\Wiki\Templates\MissionList;

/**
 * Crew & missions analytics & statistics
 */
final class Analyst
{
    /**
     * @var MissionList
     */
    private $missions;

    /**
     * @var CrewList
     */
    private $crew;

    /**
     * Best crew combination
     *
     * @var CrewMember[][][]
     */
    private $bestCrew = [];

    /**
     * Analyst constructor.
     *
     * @param MissionList $missions
     * @param CrewList    $crew
     */
    public function __construct(MissionList $missions, CrewList $crew)
    {
        $this->missions = $missions;
        $this->crew = $crew;
        // $this->crewStats();
    }//end __construct()

    /**
     * Best crew combination
     *
     * @return CrewMember[][][]
     */
    public function getBestCrew(): array
    {
        return $this->bestCrew;
    }//end rateStep()

    /**
     * Calculate & rates each crew according to their usefulness in missions,
     * and also saves a list capable crew members to each mission.
     *
     * @return void
     */
    public function crossRating(): void
    {
        $start = microtime(true);

        /* @noinspection PhpUnusedParameterInspection */
        $this->missions->eachAway(
            function (Mission $mission, $index, $episode, string $type) {
                foreach ($mission->steps as $step) {
                    $this->rateStep($step, $type);
                }//end foreach
            }
        );

        $elapsed = microtime(true) - $start;
        Log::info("cross rating calculated in {$elapsed}s");
    }//end getBestCrew()

    /**
     * Calculates crew rating to the given step
     *
     * @param MissionStep $step
     * @param string      $type
     *
     * @return void
     */
    public function rateStep(
        MissionStep $step,
        string $type
    ) {
        $this->crew->each(
            function (CrewMember $member) use ($type, $step) {
                if ('cadet' == $type) {
                    $this->rateCadetCrew($step, $member);
                } else {
                    $this->rateCrew($step, $member);
                }
            }
        );
        // assert(is_array($step['crew']));
    }//end crossRating()

    /**
     * Calculates a crew member's rating to the given cadet challenge step
     *
     * @param MissionStep $step
     * @param CrewMember  $member
     *
     * @return void
     */
    private function rateCadetCrew(MissionStep $step, CrewMember $member)
    {
        if ($member->canCriticalCadet($step)) {
            $member->incRating(2);
            $step->addCriticalCrew($member);
        } elseif ($member->canPassCadet($step)) {
            $member->incRating();
            $step->addPassCrew($member);
        }

        // as of 2016-12-18, there is no locked cadet mission steps
        // if ($member->canUnlockCadet($step)) {
        //     $member->incRating();
        //     $step->addUnlockCrew($member);
        // }
    }//end bestCrew()

    /**
     * Calculates a crew member's rating to the given step
     *
     * @param MissionStep $step
     * @param CrewMember  $member
     *
     * @return void
     */
    private function rateCrew(MissionStep $step, CrewMember $member): void
    {
        if ($member->canCritical($step)) {
            $member->incRating(2);
            $step->addCriticalCrew($member);
            $member->addCriticalMissionStep($step);
            // $this->updateCriticalCrew($step, $member);
            // unlock check is only meaningful when a member can critical
            // the step; after all, locks don't block mission pass through,
            // they just block 3 star rating
            if ($member->canUnlock($step)) {
                $member->incRating();
                $step->addUnlockCrew($member);
                $member->addUnlockMissionStep($step);
            }
        } elseif ($member->canPass($step)) {
            $member->incRating();
            $step->addPassCrew($member);
            $member->addPassMissionStep($step);
        }
    }

    /**
     * Find the best crew combination, must be called after `crossRating()`
     */
    public function bestCrew(): void
    {
        $start = microtime(true);

        $this->missions->eachAway(
            function (Mission $mission, $index, $episode, string $type) {
                foreach ($mission->steps as $idx => $step) {
                    $this->stepBestCrew($step, $idx, $index, $episode, $type);
                }//end foreach
            }
        );

        $elapsed = microtime(true) - $start;
        Log::info("best crew calculated in {$elapsed}s");
    }

    /**
     * Find the best crew combination of the given step
     *
     * @param MissionStep $step
     * @param int         $idxStep
     * @param int         $idxMission
     * @param int         $idxEpisode
     * @param string      $type
     *
     * @return void
     */
    public function stepBestCrew(
        MissionStep $step,
        int $idxStep,
        int $idxMission,
        int $idxEpisode,
        string $type
    ) {
        // just use the best member to pass this step
        /* @var CrewMember[] $member */
        $skills = $step->allSkills();
        // eligible crew is stored in each step, don't pollute the initial array
        // $member = 'cadet' == $type ? [] : $this->crew->allMax($skills);
        $member = [];

        if (!empty($step->getCrew()['unlock'])) {
            // if any alternative is locked, find best member to unlock it
            foreach ($step->locks as $idx => $lock) {
                if (empty($lock)) {
                    continue;
                }
                // $skills = Skills::skillName($step->skills[$idx]->name()[0]);
                $member = $this->bestMember(
                    $step->getCrew()['unlock'],
                    $skills,
                    $member,
                    true
                );
            }//end foreach
        }

        if (!empty($step->getCrew()['critical'])) {
            // find out best member to critical,
            $member = $this->bestMember(
                $step->getCrew()['critical'],
                $skills,
                $member
            );
        } else {
            $member = $this->bestMember(
                $step->getCrew()['pass'],
                $skills,
                $member
            );
        }

        $member = array_filter(
            $member,
            function ($member) use ($step, $type) {
                /* @var CrewMember[] $member */
                return 'cadet' == $type
                    ? $member[0]->canPassCadet($step)
                    : $member[0]->canPass($step);
            }
        );

        $this->bestCrew[$type][$idxEpisode][$idxMission][$idxStep]
            = $member;
    }//end stepBestCrew()

    /**
     * @param CrewMember[] $crew
     * @param string[]     $skills
     * @param array        $prev
     * @param bool         $lock
     *
     * @return array|null
     */
    private function bestMember(
        array $crew,
        array $skills,
        array $prev,
        bool $lock = false
    ) {
        return array_reduce(
            $crew,
            function ($prev, CrewMember $member = null) use ($skills, $lock) {
                foreach ($skills as $skill) {
                    if (empty($member->skills[$skill])) {
                        continue;
                    }
                    if (empty($prev[$member->stars])) {
                        $prev[$member->stars]
                            = [$member, max($member->skills[$skill]), $lock];

                        return $prev;
                    }
                    // as all pass-in crew member are either "critical" or "pass",
                    // we don't need to care about if they have bonus traits or not
                    $max = max($member->skills[$skill]);
                    if (($lock || empty($prev[$member->stars][2])
                         || !$prev[$member->stars][2])
                        && $max > $prev[$member->stars][1]
                    ) {
                        $prev[$member->stars] = [$member, $max, $lock];

                        return $prev;
                    }
                }//end foreach
                return $prev;
            },
            $prev
        );
    }//end best()

    /**
     * @return MissionList
     */
    public function getMissions(): MissionList
    {
        return $this->missions;
    }//end rateMissionCrew()

    /**
     * @return CrewList
     */
    public function getCrew(): CrewList
    {
        return $this->crew;
    }//end rateCadetCrew()
}//end class
