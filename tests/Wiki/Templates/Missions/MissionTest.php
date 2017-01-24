<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-28
 * Time: 21:41
 */

namespace eidng8\Tests\Wiki\Templates\Mission;

use eidng8\Log\Log;
use eidng8\Tests\TestCase;
use eidng8\Wiki\Models\Mission as MissionModel;
use eidng8\Wiki\Models\MissionCost;
use eidng8\Wiki\Models\MissionStep;
use eidng8\Wiki\Templates\Mission;

/**
 * MissionTest
 */
class MissionTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Log::forTest();
    }//end checkMissionStep()

    /**
     * @return Mission
     */
    public function testLoad()
    {
        $json = file_get_contents(
            static::DIR_CACHE
            . '/parse/Picking_the_Bones_4f51e525b22337c60d893d6744a7f579.json'
        );
        $text = json_decode($json, true)['wikitext']['*'];
        $actual = Mission::load($text);
        $this->assertInstanceOf(Mission::class, $actual);

        $model = $actual->get();
        $this->assertSame('Picking the Bones', $model->name);
        $this->assertSame('Picking the Bones', $model->page());
        $this->assertSame('Picking the Bones', $model->uri());
        $this->assertSame('Never Forgive, Never Forget', $model->episode);
        $this->assertSame(16, $model->index);
        $this->assertSame(1, $model->type);
        $this->assertInstanceOf(MissionCost::class, $model->cost);
        $this->assertInternalType('array', $model->traits);
        $this->assertNotEmpty($model->traits);
        $this->assertInternalType('array', $model->steps);
        $this->assertNotEmpty($model->steps);
        $this->assertInstanceOf(MissionStep::class, $model->steps[0]);
        $this->assertInstanceOf(MissionStep::class, $model->steps[1]);
        $this->assertInstanceOf(MissionStep::class, $model->steps[2]);
        $this->assertInstanceOf(MissionStep::class, $model->steps[3]);

        return $actual;
    }

    /**
     * @return Mission
     */
    public function testCadetLoad()
    {
        $json = file_get_contents(
            static::DIR_CACHE
            . '/parse/History_Interrupted_0638fe01cbbde6c62eb701bf86a95657.json'
        );
        $text = json_decode($json, true)['wikitext']['*'];
        $actual = Mission::load($text);
        $this->assertInstanceOf(Mission::class, $actual);

        $model = $actual->get();
        $this->assertSame('History Interrupted', $model->name);
        $this->assertSame('History Interrupted', $model->page());
        $this->assertSame('History Interrupted', $model->uri());
        $this->assertSame('The United Federation', $model->episode);
        $this->assertSame(1, $model->index);
        $this->assertSame(1, $model->type);
        $this->assertInstanceOf(MissionCost::class, $model->cost);
        $this->assertInternalType('array', $model->traits);
        $this->assertNotEmpty($model->traits);
        $this->assertInternalType('array', $model->steps);
        $this->assertNotEmpty($model->steps);

        $this->checkCadetMissionSteps($model);

        return $actual;
    }//end testLoad()

    /**
     * @param MissionModel $model
     */
    private function checkCadetMissionSteps(MissionModel $model)
    {
        $this->checkMissionStep(
            $model->steps[0],
            [
                'skills' => [
                    ['names' => ['security'], 'values' => [55, 190, 290]],
                ],
                'traits' => [
                    ['names' => ['cardassian'], 'values' => [15, 30, 60]],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[1],
            [
                'skills' => [
                    ['names' => ['science'], 'values' => [55, 190, 290]],
                ],
                'traits' => [
                    [
                        'names'  => ['borg', 'doctor', 'exobiology'],
                        'values' => [15, 30, 60],
                    ],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[2],
            [
                'skills' => [
                    ['names' => ['diplomacy'], 'values' => [55, 190, 290]],
                    ['names' => ['command'], 'values' => [55, 190, 290]],
                ],
                'traits' => [
                    ['names' => ['federation'], 'values' => [15, 30, 60]],
                    [
                        'names'  => ['bajoran', 'cardassian'],
                        'values' => [15, 30, 60],
                    ],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[3],
            [
                'skills' => [
                    ['names' => ['security'], 'values' => [55, 190, 290]],
                ],
                'traits' => [
                    ['names' => ['bajoran'], 'values' => [15, 30, 60]],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[4],
            [
                'skills' => [
                    ['names' => ['diplomacy'], 'values' => [55, 190, 290]],
                    ['names' => ['science'], 'values' => [55, 190, 290]],
                ],
                'traits' => [
                    ['names' => ['civilian'], 'values' => [15, 30, 60]],
                    ['names' => ['civilian'], 'values' => [15, 30, 60]],
                ],
            ]
        );
    }//end testCadetLoad()

    /**
     * @param MissionStep $step
     * @param array       $val
     */
    private function checkMissionStep(MissionStep $step, array $val)
    {
        $this->assertInstanceOf(MissionStep::class, $step);
        foreach ($val['skills'] as $idx => $skills) {
            $this->assertSame($skills, $step->skills[$idx]->toArray());
        }//end foreach
        foreach ($val['traits'] as $idx => $traits) {
            $this->assertSame($traits, $step->traits[$idx]->toArray());
        }//end foreach
    }//end testCadetAdvLoad()

    /**
     * @return Mission
     */
    public function testCadetAdvLoad()
    {
        $json = file_get_contents(
            static::DIR_CACHE
            . '/parse/History_Interrupted_0638fe01cbbde6c62eb701bf86a95657.json'
        );
        $text = json_decode($json, true)['wikitext']['*'];
        $actual = Mission::load($text, null, ['advanced' => true]);
        $this->assertInstanceOf(Mission::class, $actual);

        $model = $actual->get();
        $this->assertSame('History Interrupted', $model->name);
        $this->assertSame('History Interrupted', $model->page());
        $this->assertSame('History Interrupted', $model->uri());
        $this->assertSame('The United Federation', $model->episode);
        $this->assertSame(1, $model->index);
        $this->assertSame(1, $model->type);
        $this->assertInstanceOf(MissionCost::class, $model->cost);
        $this->assertInternalType('array', $model->traits);
        $this->assertNotEmpty($model->traits);
        $this->assertInternalType('array', $model->steps);
        $this->assertNotEmpty($model->steps);

        $this->checkCadetAdvMissionSteps($model);

        return $actual;
    }//end testNoCost()

    /**
     * @param MissionModel $model
     */
    private function checkCadetAdvMissionSteps(MissionModel $model)
    {
        $this->checkMissionStep(
            $model->steps[0],
            [
                'skills' => [
                    ['names' => ['security'], 'values' => [70, 170, 340]],
                ],
                'traits' => [
                    ['names' => ['cardassian'], 'values' => [15, 30, 60]],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[1],
            [
                'skills' => [
                    ['names' => ['science'], 'values' => [70, 190, 330]],
                ],
                'traits' => [
                    [
                        'names'  => ['borg', 'doctor', 'exobiology'],
                        'values' => [15, 30, 60],
                    ],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[2],
            [
                'skills' => [
                    ['names' => ['diplomacy'], 'values' => [70, 220, 340]],
                    ['names' => ['command'], 'values' => [70, 170, 340]],
                ],
                'traits' => [
                    ['names' => ['federation'], 'values' => [15, 30, 60]],
                    [
                        'names'  => ['bajoran', 'cardassian'],
                        'values' => [15, 30, 60],
                    ],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[3],
            [
                'skills' => [
                    ['names' => ['security'], 'values' => [70, 170, 340]],
                ],
                'traits' => [
                    ['names' => ['bajoran'], 'values' => [15, 30, 60]],
                ],
            ]
        );
        $this->checkMissionStep(
            $model->steps[4],
            [
                'skills' => [
                    ['names' => ['diplomacy'], 'values' => [70, 220, 340]],
                    ['names' => ['science'], 'values' => [70, 190, 330]],
                ],
                'traits' => [
                    ['names' => ['civilian'], 'values' => [15, 30, 60]],
                    ['names' => ['civilian'], 'values' => [15, 30, 60]],
                ],
            ]
        );
    }//end testNoStep()

    /**
     * @param Mission $mission
     *
     * @depends testLoad
     */
    public function testNoCost(Mission $mission)
    {
        $model = $mission->get();
        $cost = $model->cost;
        $model->cost = null;
        $this->assertFalse($model->validate());
        $model->cost = $cost;
        $this->assertTrue(
            Log::$testOutput->hasWarningThatMatches(
                '/Mission \[ Picking the Bones ] has no cost\./'
            ),
            'Mission with no cost should trigger warning.'
        );
    }//end testEmptyStep()

    /**
     * @param Mission $mission
     *
     * @depends testLoad
     */
    public function testNoStep(Mission $mission)
    {
        $model = $mission->get();
        $steps = $model->steps;
        $model->steps = null;
        $this->assertFalse($model->validate());
        $model->steps = $steps;
        $this->assertTrue(
            Log::$testOutput->hasWarningThatMatches(
                '/Mission \[ Picking the Bones ] has no step\./'
            ),
            'Mission with no step should trigger warning.'
        );
    }//end testInvalidStep()

    /**
     * @param Mission $mission
     *
     * @depends testLoad
     */
    public function testEmptyStep(Mission $mission)
    {
        $model = $mission->get();
        $step = $model->steps[0];
        $model->steps[0] = null;
        $this->assertFalse($model->validate());
        $model->steps[0] = $step;
        $this->assertTrue(
            Log::$testOutput->hasWarningThatMatches(
                '/Mission \[ Picking the Bones ] step \( 0 \) is empty\./'
            ),
            'Empty step should trigger warning.'
        );
    }//end testToArray()

    /**
     * @param Mission $mission
     *
     * @depends testLoad
     */
    public function testInvalidStep(Mission $mission)
    {
        $model = $mission->get();
        $skills = $model->steps[1]->skills;
        $model->steps[1]->skills = null;
        $this->assertFalse($model->validate());
        $model->steps[1]->skills = $skills;
        $this->assertTrue(
            Log::$testOutput->hasWarningThatMatches(
                '/Mission \[ Picking the Bones ] step \( 1 \) is invalid\./'
            ),
            'Invalid step should trigger warning.'
        );
    }//end checkCadetMissionSteps()

    public function testToArray()
    {
        $json = file_get_contents(
            static::DIR_CACHE
            . '/parse/Picking_the_Bones_4f51e525b22337c60d893d6744a7f579.json'
        );
        $text = json_decode($json, true)['wikitext']['*'];
        $actual = Mission::load($text);
        $actual = $actual->get()->toArray();
        $this->assertInternalType('array', $actual);
        $this->assertInternalType('array', $actual['cost']);
        $this->assertInternalType('array', $actual['traits']);
        $this->assertInternalType('array', $actual['steps']);
        $this->assertInternalType('array', $actual['steps'][0]);
        $this->assertInternalType('array', $actual['steps'][1]);
        $this->assertInternalType('array', $actual['steps'][2]);
        $this->assertInternalType('array', $actual['steps'][3]);
    }//end checkCadetAdvMissionSteps()
}//end class
