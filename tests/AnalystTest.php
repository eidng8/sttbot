<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-11
 * Time: 09:54
 */

namespace eidng8\Tests;

use eidng8\Log\Log;
use eidng8\Wiki\Analyst;
use eidng8\Wiki\Models\CrewMember;
use eidng8\Wiki\Models\Mission;

/**
 * AnalystTest
 */
class AnalystTest extends TestCase
{
    private $wiki;

    /**
     * AnalystTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->wiki = $this->newWikiInstance();
    }//end __construct()

    public function testRateStep()
    {
        $analyst
            = new Analyst($this->wiki->missions(), $this->wiki->crew());

        $mission = $this->wiki->missions()->byName('Back to School');
        $idxStep = 1;
        $idxMission = $mission->index - 1;
        $idxEpisode = 1;
        $step = $mission->steps[$idxStep];
        $analyst->rateStep($step, 'episode');
        $analyst->stepBestCrew(
            $step,
            $idxStep,
            $idxMission,
            $idxEpisode,
            'episode'
        );

        $this->assertArrayHasKey('pass', $step['crew']);
        $this->assertNotEmpty($step['crew']['pass']);

        $this->assertArrayHasKey('critical', $step['crew']);
        $this->assertNotEmpty($step['crew']['critical']);

        $this->assertNotEmpty(
            $analyst->getBestCrew(
            )['episode'][$idxEpisode][$idxMission][$idxStep]
        );
        $this->assertSame(
            'Chancellor Gowron',
            $analyst->getBestCrew(
            )['episode'][$idxEpisode][$idxMission][$idxStep][5][0]->name
        );
    }//end testRateStep()

    public function testRateStepCadet()
    {
        $analyst
            = new Analyst($this->wiki->missions(), $this->wiki->crew());

        $mission = $this->wiki->missions()->byName('First Conflict');
        $idxStep = 2;
        $idxMission = $mission->index - 1;
        $idxChallenge = 1;
        $step = $mission->steps[$idxStep];
        $analyst->rateStep($step, 'cadet');
        $analyst->stepBestCrew(
            $step,
            $idxStep,
            $idxMission,
            $idxChallenge,
            'cadet'
        );

        // $this->assertArrayHasKey('pass', $step['crew']);
        // $this->assertNotEmpty($step['crew']['pass']);

        $this->assertArrayHasKey('critical', $step['crew']);
        $this->assertNotEmpty($step['crew']['critical']);

        $this->assertNotEmpty(
            $analyst->getBestCrew(
            )['cadet'][$idxChallenge][$idxMission][$idxStep]
        );
        $this->assertSame(
            'Dr Phlox',
            $analyst->getBestCrew(
            )['cadet'][$idxChallenge][$idxMission][$idxStep][2][0]->name
        );
    }//end testRateStepCadet()

    /**
     * @return Analyst
     */
    public function testCrossRating()
    {
        Log::forTest();
        $analyst = new Analyst($this->wiki->missions(), $this->wiki->crew());
        $this->assertInstanceOf(Analyst::class, $analyst);
        $analyst->crossRating();

        return $analyst;
    }//end testCrossRating()

    /**
     * @param Analyst $analyst
     *
     * @depends testCrossRating
     */
    public function testPassAndCriticalCrewDoNotOverlap(Analyst $analyst)
    {
        $this->assertNotEmpty($analyst->getCrew());
        $analyst->getMissions()->eachAway(
            function (Mission $mission) {
                foreach ($mission->steps as $idx => $step) {
                    $msg = "Mission $mission->name ; step $idx";
                    $msg .= " [ {$step->skills[0]['names'][0]}";
                    $msg .= ":{$step->skills[0]['values'][2]} ]";
                    $this->assertArrayHasKey('crew', $step, $msg);
                    $this->assertInternalType(
                        'array',
                        $step['crew'],
                        "$msg ; 'crew' should be array"
                    );
                    // $this->assertArrayHasKey('pass', $step['crew'], $msg);
                    // $this->assertNotEmpty($step['crew']['pass'], $msg);
                    // it is possible to have no 'pass' crew,
                    // coz every one can reach critical rate
                    $this->assertFalse(
                        empty($step['crew']['pass'])
                        && empty($step['crew']['critical']),
                        "$msg ; 'pass' & 'critical' aren't both empty"
                    );

                    // as of 2016-12-17, there are some missions that
                    // no one can reach critical rate,
                    // using 1.5x critical factor.
                    if (empty($step['crew']['pass'])
                        || empty($step['crew']['critical'])
                    ) {
                        continue;
                    }

                    //
                    $pass = array_column($step['crew']['pass'], 'name');
                    $critical = array_column($step['crew']['critical'], 'name');
                    $this->assertEmpty(
                        array_intersect($pass, $critical),
                        "$msg 'critical' & 'pass' crew should not overlap"
                    );
                }//end foreach
            }
        );
    }//end testPassAndCriticalCrewDoNotOverlap()

    /**
     * @param Analyst $analyst
     *
     * @return \eidng8\Wiki\Models\CrewMember[][][]
     * @depends testCrossRating
     */
    public function testBestCrew(Analyst $analyst)
    {
        $analyst->bestCrew();
        $crew = $analyst->getBestCrew();
        $this->assertNotEmpty($crew);

        return $crew;
    }//end testBestCrew()

    /**
     * @param array $crew
     *
     * @depends testBestCrew
     */
    public function testEpisodeCrew(array $crew)
    {
        $missions = $this->wiki->missions()->get('episodes');
        foreach ($crew['episodes'] as $idxEpi => $episode) {
            foreach ($episode as $idxMis => $mission) {
                foreach ($mission as $idxStep => $step) {
                    $msg = "Episode $idxEpi";
                    $msg .= " Mission {$missions[$idxEpi][$idxMis]->name}";
                    $msg .= " step $idxStep";
                    $this->assertNotEmpty(
                        $step,
                        "$msg steps should not be empty"
                    );
                    foreach ($step as $idxMember => $member) {
                        $msg .= " member $idxMember";

                        /* @var CrewMember[] $member */
                        $this->assertInstanceOf(
                            CrewMember::class,
                            $member[0],
                            "$msg should be instance of CrewMember"
                        );
                        $this->assertTrue(
                            $member[0]->canPass(
                                $missions[$idxEpi][$idxMis]->steps[$idxStep]
                            ),
                            "$msg should be able to pass the step"
                        );
                    }//end foreach
                }//end foreach
            }//end foreach
        }//end foreach
    }//end testEpisodeCrew()

    /**
     * @param array $crew
     *
     * @depends testBestCrew
     */
    public function testCadetCrew(array $crew)
    {
        $missions = $this->wiki->missions()->get('cadet');
        foreach ($crew['cadet'] as $idxCh => $challenge) {
            foreach ($challenge as $idxMis => $mission) {
                foreach ($mission as $idxStep => $step) {
                    $msg = "Challenge $idxCh";
                    $msg .= " Mission {$missions[$idxCh][$idxMis]->name}";
                    $msg .= " step $idxStep";
                    $this->assertNotEmpty(
                        $step,
                        "$msg should not be empty"
                    );
                    foreach ($step as $idxMember => $member) {
                        $msg .= " member $idxMember";

                        /* @var CrewMember[] $member */
                        $this->assertInstanceOf(
                            CrewMember::class,
                            $member[0],
                            "$msg should be instance of CrewMember"
                        );
                        $this->assertTrue(
                            $member[0]->canPass(
                                $missions[$idxCh][$idxMis]->steps[$idxStep]
                            ),
                            "$msg should be able to pass the step"
                        );
                    }//end foreach
                }//end foreach
            }//end foreach
        }//end foreach
    }//end testCadetCrew()
}//end class
