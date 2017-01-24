<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-18
 * Time: 22:46
 */

namespace eidng8\Tests\Wiki\Missions;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Models\Mission;
use eidng8\Wiki\Models\MissionCost;
use eidng8\Wiki\Models\MissionStep;
use eidng8\Wiki\Models\ReqAndBonus;
use eidng8\Wiki\Templates\MissionList;

/**
 * MissionListTest
 */
class MissionListTest extends TestCase
{
    private $wiki;

    /**
     * MissionListTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->wiki = $this->newWikiInstance();
    }//end __construct()

    /**
     * @return MissionList
     */
    public function testCreate()
    {
        $api = $this->newApiInstance();
        $missions = new MissionList(
            $api->parse(),
            $api->query(),
            $api->expandTemplates()
        );
        $this->assertNotEmpty($missions->fetch());

        return $missions;
    }//end testCreate()

    /**
     * @depends testCreate
     *
     * @param MissionList $missions
     */
    public function testGetHasLock(MissionList $missions)
    {
        $list = $missions->get();
        $this->assertTrue(is_array($list));
        $this->assertTrue(is_string(array_keys($list)[0]));
        $this->assertTrue(is_array(array_values($list)[0]));

        $mission = $missions->byName('Cardassian Hospitality');
        $this->assertNotEmpty($mission->steps[1]['locks']);

        $test = $this;
        $missions->each(
            function (Mission $mission) use ($test) {
                $test->assertNotEmpty($mission->cost->normal());
                if ($mission->traits) {
                    $test->assertSame(
                        $mission->traits,
                        array_filter($mission->traits)
                    );
                }
                if ($mission->locks) {
                    $test->assertSame(
                        $mission->locks,
                        array_filter($mission->locks)
                    );
                }
            }
        );
    }//end testGetHasLock()

    /**
     * @depends testCreate
     *
     * @param MissionList $missions
     */
    public function testNameShouldNotContainTemplate(MissionList $missions)
    {
        $regex = '/.*\{\{pagename}}.*/i';
        $missions->each(
            function (Mission $mission, $idx, $episode, $type) use ($regex) {
                $this->assertNotRegExp(
                    $regex,
                    $mission->name,
                    "$type #$episode mission #$idx name should not contain template"
                );
                $this->assertNotRegExp(
                    $regex,
                    $mission->page,
                    "$type #$episode mission #$idx page should not contain template"
                );
            }
        );
    }//end testNameShouldNotBeTemplate()

    /**
     * @depends testCreate
     *
     * @param MissionList $missions
     */
    public function testCadet(MissionList $missions)
    {
        $model = $missions->byName(
            'First Conflict',
            'The United Federation'
        );
        $this->checkCadetBasic($model);
        $this->assertSame('The United Federation', $model->episode);

        $this->checkCadetStep1($model);
        $this->checkCadetStep2($model);
        $this->checkCadetStep3($model);
        $this->checkCadetStep4($model);
        $this->checkCadetStep5($model);
    }//end testCadet()

    /**
     * @param Mission $model
     */
    private function checkCadetBasic(Mission $model)
    {
        $this->assertInstanceOf(Mission::class, $model);
        $this->assertSame('First Conflict', $model->name);
        $this->assertSame('First Conflict', $model->page());
        $this->assertSame('First Conflict', $model->uri());

        $this->assertSame(3, $model->index);
        $this->assertSame(1, $model->type);
        $this->assertInstanceOf(MissionCost::class, $model->cost);
        $this->assertTrue($model->cost->useTicket());
        $this->assertSame(1, $model->cost->ticket());

        $this->assertInternalType('array', $model->traits);
        $this->assertNotEmpty($model->traits);
        $this->assertSame(
            [
                'astrophysicist',
                'crafty',
                'cyberneticist',
                'federation',
                'gambler',
            ],
            $model->traits
        );

        $this->assertInternalType('array', $model->steps);
        $this->assertCount(5, $model->steps);
    }//end testCadetAdv()

    /**
     * @param Mission $model
     */
    private function checkCadetStep1(Mission $model)
    {
        $step = $model->steps[0];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(1, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['diplomacy'],
                'values' => [85, 215, 325],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertCount(1, $step->traits);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[0]
        );
        $this->assertSame(
            [
                'names'  => ['astrophysicist'],
                'values' => [20, 40, 70],
            ],
            $step->traits[0]->toArray()
        );
    }//end testExport()

    /**
     * @param Mission $model
     */
    private function checkCadetStep2(Mission $model)
    {
        $step = $model->steps[1];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(1, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['security'],
                'values' => [85, 215, 325],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertCount(1, $step->traits);
        $this->assertNull($step->traits[0]);
    }//end checkCadetBasic()

    /**
     * @param Mission $model
     */
    private function checkCadetStep3(Mission $model)
    {
        $step = $model->steps[2];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(2, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['medicine'],
                'values' => [85, 215, 325],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[1]
        );
        $this->assertSame(
            [
                'names'  => ['engineering'],
                'values' => [85, 215, 325],
            ],
            $step->skills[1]->toArray()
        );
        $this->assertCount(2, $step->traits);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[0]
        );
        $this->assertSame(
            [
                'names'  => ['gambler'],
                'values' => [20, 40, 70],
            ],
            $step->traits[0]->toArray()
        );
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[1]
        );
        $this->assertSame(
            [
                'names'  => ['cyberneticist'],
                'values' => [20, 40, 70],
            ],
            $step->traits[1]->toArray()
        );
    }//end testCadetStep1()

    /**
     * @param Mission $model
     */
    private function checkCadetStep4(Mission $model)
    {
        $step = $model->steps[3];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(2, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['security'],
                'values' => [85, 215, 325],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[1]
        );
        $this->assertSame(
            [
                'names'  => ['diplomacy'],
                'values' => [85, 215, 325],
            ],
            $step->skills[1]->toArray()
        );
        $this->assertCount(2, $step->traits);
        $this->assertNull($step->traits[0]);
        $this->assertSame(
            [
                'names'  => ['crafty'],
                'values' => [20, 40, 70],
            ],
            $step->traits[1]->toArray()
        );
    }//end testCadetStep2()

    /**
     * @param Mission $model
     */
    private function checkCadetStep5(Mission $model)
    {
        $step = $model->steps[4];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(1, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['science'],
                'values' => [85, 215, 325],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertCount(1, $step->traits);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[0]
        );
        $this->assertSame(
            [
                'names'  => ['federation'],
                'values' => [20, 40, 70],
            ],
            $step->traits[0]->toArray()
        );
    }//end testCadetStep3()

    /**
     * @depends testCreate
     *
     * @param MissionList $missions
     */
    public function testCadetAdv(MissionList $missions)
    {
        $model = $missions->byName(
            'First Conflict',
            'Adv: United Federation'
        );
        $this->checkCadetBasic($model);
        $this->assertSame('Adv: United Federation', $model->episode);

        $this->checkCadetAdvStep1($model);
        $this->checkCadetAdvStep2($model);
        $this->checkCadetAdvStep3($model);
        $this->checkCadetAdvStep4($model);
        $this->checkCadetAdvStep5($model);
    }//end testCadetStep4()

    /**
     * @param Mission $model
     */
    private function checkCadetAdvStep1(Mission $model)
    {
        $step = $model->steps[0];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(1, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['diplomacy'],
                'values' => [120, 250, 375],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertCount(1, $step->traits);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[0]
        );
        $this->assertSame(
            [
                'names'  => ['astrophysicist'],
                'values' => [0, 0, 0],
            ],
            $step->traits[0]->toArray()
        );
    }//end testCadetStep5()

    /**
     * @param Mission $model
     */
    private function checkCadetAdvStep2(Mission $model)
    {
        $step = $model->steps[1];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(1, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['security'],
                'values' => [110, 200, 350],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertCount(1, $step->traits);
        $this->assertNull($step->traits[0]);
    }//end testCadetAdvStep1()

    /**
     * @param Mission $model
     */
    private function checkCadetAdvStep3(Mission $model)
    {
        $step = $model->steps[2];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(2, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['medicine'],
                'values' => [100, 240, 350],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[1]
        );
        $this->assertSame(
            [
                'names'  => ['engineering'],
                'values' => [110, 240, 350],
            ],
            $step->skills[1]->toArray()
        );
        $this->assertCount(2, $step->traits);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[0]
        );
        $this->assertSame(
            [
                'names'  => ['gambler'],
                'values' => [0, 0, 0],
            ],
            $step->traits[0]->toArray()
        );
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[1]
        );
        $this->assertSame(
            [
                'names'  => ['cyberneticist'],
                'values' => [0, 0, 0],
            ],
            $step->traits[1]->toArray()
        );
    }//end testCadetAdvStep2()

    /**
     * @param Mission $model
     */
    private function checkCadetAdvStep4(Mission $model)
    {
        $step = $model->steps[3];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(2, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['security'],
                'values' => [110, 200, 350],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[1]
        );
        $this->assertSame(
            [
                'names'  => ['diplomacy'],
                'values' => [120, 250, 375],
            ],
            $step->skills[1]->toArray()
        );
        $this->assertCount(2, $step->traits);
        $this->assertNull($step->traits[0]);
        $this->assertSame(
            [
                'names'  => ['crafty'],
                'values' => [0, 0, 0],
            ],
            $step->traits[1]->toArray()
        );
    }//end testCadetAdvStep3()

    /**
     * @param Mission $model
     */
    private function checkCadetAdvStep5(Mission $model)
    {
        $step = $model->steps[4];
        $this->assertInstanceOf(MissionStep::class, $step);
        $this->assertCount(1, $step->skills);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->skills[0]
        );
        $this->assertSame(
            [
                'names'  => ['science'],
                'values' => [100, 235, 330],
            ],
            $step->skills[0]->toArray()
        );
        $this->assertCount(1, $step->traits);
        $this->assertInstanceOf(
            ReqAndBonus::class,
            $step->traits[0]
        );
        $this->assertSame(
            [
                'names'  => ['federation'],
                'values' => [0, 0, 0],
            ],
            $step->traits[0]->toArray()
        );
    }//end testCadetAdvStep4()

    public function testExport()
    {
        $missions = $this->wiki->missions()->export();

        $this->assertInternalType('array', $missions);
        $this->assertInternalType('array', $missions[0]);
        $this->assertInternalType('array', $missions[1]);

        $this->assertNotEmpty($missions);
        $this->assertNotEmpty($missions[0]);
        $this->assertNotEmpty($missions[1]);

        foreach ($missions[1] as $idx => $mission) {
            $this->checkExportedMission($mission, $idx);
        }//end foreach
    }//end testCadetAdvStep5()

    /**
     * @param array $mission
     * @param int   $idx
     */
    private function checkExportedMission(array $mission, int $idx)
    {
        $this->checkExportedMissionName($mission, $idx);
        $this->checkExportedMissionType($mission);
        $this->checkExportedMissionCost($mission);

        // only away team mission has attributes below
        if (Mission::AWAY_TEAM !== $mission['type']) {
            return;
        }

        $this->checkExportedMissionRequirements($mission);
        // $this->checkExportedMissionBonus($mission);

        $this->checkExportedMissionSteps($mission);
    }//end checkExportedMission()

    /**
     * @param array $mission
     * @param int   $idx
     */
    private function checkExportedMissionName(array $mission, int $idx)
    {
        $this->assertArrayHasKey('name', $mission);
        $this->assertInternalType(
            'string',
            $mission['name'],
            "Mission[$idx] 'name' should be string"
        );
        $this->assertNotEmpty(
            $mission['name'],
            "Mission[$idx] 'name' should not be empty"
        );
    }//end checkExportedMissionName()

    /**
     * @param array $mission
     */
    private function checkExportedMissionType(array $mission)
    {
        $this->assertArrayHasKey('type', $mission);
        $this->assertInternalType(
            'integer',
            $mission['type'],
            "$mission[name] 'type' should be integer"
        );
        $this->assertGreaterThanOrEqual(
            Mission::AWAY_TEAM,
            $mission['type'],
            "$mission[name] 'type' should be >= " . Mission::AWAY_TEAM
        );
        $this->assertLessThanOrEqual(
            Mission::SPACE_BATTLE,
            $mission['type'],
            "$mission[name] 'type' should be <= " . Mission::SPACE_BATTLE
        );
    }//end checkExportedMissionType()

    /**
     * @param array $mission
     */
    private function checkExportedMissionCost(array $mission)
    {
        $this->assertArrayHasKey(
            'cost',
            $mission,
            "$mission[name] should has 'cost'"
        );
        $this->assertInternalType(
            'array',
            $mission['cost'],
            "$mission[name] 'cost' should be array"
        );
        $this->assertNotEmpty(
            $mission['cost'],
            "$mission[name] 'cost' should not be empty"
        );
        foreach ($mission['cost'] as $idx => $cost) {
            $this->assertInternalType(
                'integer',
                $cost,
                "$mission[name] 'cost[$idx]' should be integer"
            );
            $this->assertGreaterThan(
                0,
                $cost,
                "$mission[name] 'cost[$idx]' should be greater than 0"
            );
        }//end foreach
    }//end checkExportedMissionCost()

    /**
     * @param array $mission
     */
    private function checkExportedMissionRequirements(array $mission)
    {
        $this->assertArrayHasKey(
            'requirement',
            $mission,
            "$mission[name] should has 'requirement'"
        );
        $this->assertInternalType(
            'array',
            $mission['requirement'],
            "$mission[name] 'requirement' should be array"
        );
        $this->assertNotEmpty(
            $mission['requirement'],
            "$mission[name] 'requirement' should not be empty"
        );
        foreach ($mission['requirement'] as $idx => $requirement) {
            $this->assertInternalType(
                'integer',
                $requirement,
                "$mission[name] 'requirement[$idx]' should be integer"
            );
            $this->assertGreaterThan(
                0,
                $requirement,
                "$mission[name] 'requirement[$idx]' should be greater than 0"
            );
        }//end foreach

        // cadet missions has only 1 cost
        if (count($mission['cost']) > 1) {
            $this->assertSame(
                count($mission['cost']),
                count($mission['requirement']),
                "$mission[name] cost & requirement should have same count"
            );
        }
    }//end checkExportedMissionRequirements()

    /**
     * @param array $mission
     */
    private function checkExportedMissionSteps(array $mission)
    {
        $this->assertArrayHasKey(
            'steps',
            $mission,
            "$mission[name] should has 'steps'"
        );
        $this->assertInternalType(
            'array',
            $mission['steps'],
            "$mission[name] 'steps' should be array"
        );
        $this->assertNotEmpty(
            $mission['steps'],
            "$mission[name] 'steps' should not be empty"
        );

        foreach ($mission['steps'] as $idx => $step) {
            $this->assertInternalType(
                'array',
                $step,
                "$mission[name] 'step[$idx]' should be array"
            );
            $this->assertNotEmpty(
                $step,
                "$mission[name] 'step[$idx]' should not be empty"
            );
            $this->assertInternalType(
                'array',
                $step['skills'],
                "$mission[name] 'step[$idx]' skills should be array"
            );
            $this->assertNotEmpty(
                $step['skills'],
                "$mission[name] 'step[$idx]' should has skills"
            );
            // $this->assertInternalType(
            //     'array',
            //     $step['traits'],
            //     "$mission[name] 'step[$idx]' traits should be array"
            // );
            // $this->assertNotEmpty(
            //     $step['traits'],
            //     "$mission[name] 'step[$idx]' should has traits"
            // );
            // $this->assertInternalType(
            //     'array',
            //     $step['locks'],
            //     "$mission[name] 'step[$idx]' locks should be array"
            // );
            // $this->assertNotEmpty(
            //     $step['locks'],
            //     "$mission[name] 'step[$idx]' should has locks"
            // );

            foreach ($step['skills'] as $sidx => $skill) {
                $this->assertInternalType(
                    'string',
                    $skill,
                    "$mission[name] 'step[$idx]' 'skill[$sidx]' skills should be string"
                );
                $this->assertNotEmpty(
                    $skill,
                    "$mission[name] 'step[$idx]' 'skill[$sidx]' should not be empty"
                );
            }//end foreach
        }//end foreach
    }//end checkExportedMissionSteps()
}//end class
