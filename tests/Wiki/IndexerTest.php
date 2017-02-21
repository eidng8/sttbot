<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2017-02-06
 * Time: 17:35
 */

namespace eidng8\Tests\Wiki;

use eidng8\Tests\TestCase;
use eidng8\Wiki;
use eidng8\Wiki\Indexer;
use eidng8\Wiki\Models\Skills;

class IndexerTest extends TestCase
{
    /**
     * @var Wiki
     */
    private $wiki;

    /**
     * @var Indexer
     */
    private $indexer;

    public function __construct()
    {
        parent::__construct();
        $this->wiki = $this->newWikiInstance();
        $this->indexer = new Indexer();
    }//end __construct()

    public function testConstruct()
    {
        $indexer = new Indexer();
        $this->assertInstanceOf(Indexer::class, $indexer);
        $this->assertInternalType('array', $indexer->skillIndex);
    }//end testConstruct()

    public function testLoadSkills()
    {
        foreach ($this->indexer->skillIndex as $abbr => $idx) {
            $this->assertInternalType('string', $abbr);
            $this->assertInternalType('integer', $idx);
            $this->assertSame(Skills::SKILLS[$idx], $abbr);
        }
    }//end testLoadSkills()

    public function testLoadCrew()
    {
        $this->indexer->loadCrew($this->wiki->analyse()->getCrew()->export());
        $this->checkCharacters();
        $this->checkRaces();
        $this->checkTraits();
    }//end testLoadCrew()

    public function testLoadMissions()
    {
        $this->indexer->loadMissions(
            $this->wiki->analyse()->getMissions()->export()[1]
        );
        foreach ($this->indexer->missionIndex as $mission => $idx) {
            $this->assertInternalType('string', $mission);
            $this->assertInternalType('integer', $idx);
        }
    }//end testLoadMissions()

    public function testCharacter()
    {
        $this->assertSame(-1, $this->indexer->character('does not exist'));
    }//end testCharacter()

    public function testCrew()
    {
        $this->assertSame(-1, $this->indexer->crew('does not exist'));
    }//end testCrew()

    public function testMission()
    {
        $this->assertSame(-1, $this->indexer->mission('does not exist'));
    }//end testMission()

    public function testRace()
    {
        $this->assertSame(-1, $this->indexer->race('does not exist'));
    }//end testRace()

    public function testSkill()
    {
        $this->assertSame(-1, $this->indexer->skill('does not exist'));
    }//end testSkill()

    public function testTrait()
    {
        $this->assertSame(-1, $this->indexer->trait('does not exist'));
    }//end testTrait()

    private function checkCharacters()
    {
        foreach ($this->indexer->charIndex as $char => $idx) {
            $this->assertInternalType('string', $char);
            $this->assertInternalType('integer', $idx);
        }
    }//end checkCharacters()

    private function checkRaces()
    {
        foreach ($this->indexer->raceIndex as $race => $idx) {
            $this->assertInternalType('string', $race);
            $this->assertInternalType('integer', $idx);
        }
    }//end checkRaces()

    private function checkTraits()
    {
        foreach ($this->indexer->charIndex as $trait => $idx) {
            $this->assertInternalType('string', $trait);
            $this->assertInternalType('integer', $idx);
        }
    }//end checkTraits()
}//end class
