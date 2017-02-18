<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2017-02-07
 * Time: 14:42
 */

namespace eidng8\Tests\Wiki;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Exporter;
use eidng8\Wiki\Indexer;
use eidng8\Wiki\Models\CrewMember;
use eidng8\Wiki\Models\Mission;
use eidng8\Wiki\Models\MissionStep;
use eidng8\Wiki\Models\Skills;

class ExporterTest extends TestCase
{
    /**
     * @var \eidng8\Wiki
     */
    private static $wiki;


    protected function setUp()
    {
        parent::setUp();

        if (!static::$wiki) {
            static::$wiki = $this->newWikiInstance();
        }
    }//end setUp()

    public function testConstruct()
    {
        $exporter = new Exporter(static::$wiki->analyse(), new Indexer());
        $this->assertInstanceOf(Exporter::class, $exporter);
    }//end testConstruct()

    public function testExport()
    {
        $startTime = time();
        $exporter = new Exporter(static::$wiki->analyse());
        $export = $exporter->export();
        $this->assertInternalType('array', $export);
        $this->assertArrayHasKey('version', $export);
        $this->assertSame(STTBOT_VERSION, $export['version']);
        $this->assertArrayHasKey('generatedAt', $export);
        $this->assertGreaterThanOrEqual($startTime, $export['generatedAt']);
        $this->assertArrayHasKey('characters', $export);
        $this->assertArrayHasKey('crew', $export);
        $this->assertArrayHasKey('episodes', $export);
        $this->assertArrayHasKey('missions', $export);
        $this->assertArrayHasKey('races', $export);
        $this->assertArrayHasKey('skills', $export);
        $this->assertArrayHasKey('traits', $export);
        return $export;
    }//end testExport()

    /**
     * @depends testExport
     *
     * @param array $exported
     */
    public function testExportedCharacters(array $exported)
    {
        $this->assertSame(
            count(static::$wiki->crew()->get()),
            count($exported['crew'])
        );
        foreach ($exported['crew'] as $member) {
            $expected = static::$wiki->crew()->byName($member['name']);
            $this->assertInstanceOf(CrewMember::class, $expected);
            $this->checkArrayKeys(
                [
                    'character',
                    'name',
                    'picture',
                    'race',
                    'skills',
                    'stars',
                    'traits'
                ],
                $member
            );
            $this->assertSame($expected->picture, $member['picture']);
            $this->assertSame(
                $expected->character,
                $exported['characters'][$member['character']]
            );
            $this->assertSame(
                $expected->race,
                $exported['races'][$member['race']]
            );
            $this->checkMemberSkills($expected, $exported, $member);
            $this->checkMemberTraits($expected, $exported, $member);
        }//end foreach
    }//end testExportedCharacters()

    /**
     * @depends testExport
     *
     * @param array $exported
     */
    public function testExportedMissions(array $exported)
    {
        $this->removeMissionTraitRich();

        $idx = 0;
        static::$wiki->missions()->each(
            function (Mission $expected) use ($exported, &$idx) {
                $mission = $exported['missions'][$idx++];
                $this->assertSame($expected->name, $mission['name']);
                $this->assertSame($expected->type, $mission['type']);
                $this->assertSame(
                    $expected->episode,
                    $exported['episodes'][$mission['episode']]
                );
                $this->assertSame($expected->cost['values'], $mission['cost']);

                if (Mission::SPACE_BATTLE === $mission['type']) {
                    return;
                }

                foreach ($mission['steps'] as $sidx => $step) {
                    $this->checkMissionStep(
                        $expected->steps[$sidx],
                        $step,
                        $exported
                    );
                }//end foreach
            }
        );

        $this->assertSame(
            $idx,
            count($exported['missions'])
        );
    }//end testExportedMissions()

    private function checkMemberSkills(
        CrewMember $expected,
        array $exported,
        array $member
    ) {
        foreach ($member['skills'] as $index => $skill) {
            $this->assertSame(
                $expected->skills[$exported['skills'][$index]][1],
                $skill
            );
        }//end foreach
    }//end checkMemberSkills()

    private function checkMemberTraits(
        CrewMember $expected,
        array $exported,
        array $member
    ) {
        $traits = array_map(
            function ($idx) use ($exported) {
                return $exported['traits'][$idx];
            },
            $member['traits']
        );
        $this->assertEquals($expected->traits, $traits);
    }//end checkMemberTraits()

    private function checkMissionStep(
        MissionStep $expected,
        array $step,
        array $exported
    ) {
        $this->checkMissionStepSkills($expected, $step, $exported);
        $this->checkMissionStepTraits($expected, $step, $exported);
        $this->checkMissionStepCrew($expected, $step, $exported);
    }//end checkMissionStep()

    private function checkMissionStepSkills(
        MissionStep $expected,
        array $step,
        array $exported
    ) {
        foreach ($step['skills'] as $idx => $skill) {
            $this->assertSame(
                Skills::skillName($expected['skills'][$idx]['names'][0]),
                $exported['skills'][$skill]
            );
            $this->assertSame(
                $expected['skills'][$idx]['values'],
                $step['req'][$idx]
            );
        }//end foreach
    }//end checkMissionStep()

    private function checkMissionStepTraits(
        MissionStep $expected,
        array $step,
        array $exported
    ) {
        foreach ($step['traits'] as $idx => $trait) {
            if (empty($trait)) {
                continue;
            }
            $actual = array_map(
                function ($trait) use ($exported) {
                    return $exported['traits'][$trait];
                },
                $trait
            );
            $this->assertSame($expected['traits'][$idx]['names'], $actual);
            $this->assertSame(
                $expected['traits'][$idx]['values'],
                $step['bonus'][$idx]
            );
        }//end foreach
    }//end checkMissionStepTraits()

    private function checkMissionStepCrew(
        MissionStep $expected,
        array $step,
        array $exported
    ) {
        $this->assertSame(count($expected->getCrew()), count($step['crew']));
        foreach ($step['crew'] as $type => $crew) {
            $this->assertSame(count($expected->getCrew()[$type]), count($crew));
            foreach ($crew as $idx => $member) {
                $this->assertSame(
                    $exported['crew'][$member]['name'],
                    $expected->getCrew()[$type][$idx]->name
                );
            }//end foreach
        }//end foreach
    }//end checkMissionStepCrew()

    private function removeMissionTraitRich()
    {
        // as of 2017-2-15, no one possesses the 'rich' trait.
        $mission = static::$wiki->missions()->byName('Under New Management');
        $mission->steps[1]->traits[0]['names']
            = array_slice($mission->steps[1]->traits[0]['names'], 0, 2);

        $mission = static::$wiki->missions()->byName('For the People');
        $mission->steps[1]->traits[0]['names']
            = array_slice($mission->steps[1]->traits[0]['names'], 0, 1);
        $mission->steps[2]->traits[1]['names']
            = array_slice($mission->steps[2]->traits[1]['names'], 0, 1);

        $mission = static::$wiki->missions()->byName('Operation Isolate');
        $mission->steps[1]->traits[0]['names']
            = array_slice($mission->steps[1]->traits[0]['names'], 0, 1);
    }//end removeMissionTraitRich()
}//end class
