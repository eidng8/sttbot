<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-05
 * Time: 19:43
 */

namespace eidng8\Tests\Wiki\Templates;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Models\CrewMember;
use eidng8\Wiki\Models\Skills;
use eidng8\Wiki\Templates\CrewList;

/**
 * CrewListTest
 */
class CrewListTest extends TestCase
{
    private $wiki;

    /**
     * CrewListTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->wiki = $this->newWikiInstance();
    }//end __construct()

    public function testNew()
    {
        $api = $this->newApiInstance();
        $text = (file_get_contents(
            static::DIR_SAMPLE . '/Crew-crew_member_list.txt'
        ));

        $tmpl = new CrewList($text, $api->parse(), $api->query());
        $got = array_values($tmpl->get());
        $this->assertSame(2, count($got));

        $this->assertInstanceOf(CrewMember::class, $got[0]);
        $this->assertSame('"Dark Ages" McCoy', $got[0]->name);
        $this->assertSame(
            'McCoy',
            $tmpl->byName('"Dark Ages" McCoy')->character
        );
        // @codingStandardsIgnoreStart
        $this->assertSame(
            'http://startrektimelineswiki.com/w/images/thumb/2/25/%22Dark_Ages%22_McCoy_head.png/100px-%22Dark_Ages%22_McCoy_head.png',
            $tmpl->byName('"Dark Ages" McCoy')->picture
        );
        // @codingStandardsIgnoreEnd
        $this->assertInstanceOf(Skills::class, $got[0]->skills);
        $this->assertSame(
            [
                'dip' => [786 + 84, 786 + 188],
                'med' => [972 + 201, 972 + 503],
                'sec' => [745 + 187, 745 + 332],
            ],
            $got[0]->skills->toArray()
        );
        $this->assertSame(
            ['civilian', 'doctor', 'federation', 'human', 'starfleet'],
            $got[0]->traits
        );

        $this->assertInstanceOf(CrewMember::class, $got[1]);
        $this->assertSame('Changeling Bashir', $got[1]->name);
        $this->assertSame(
            'Changeling',
            $tmpl->byName('Changeling Bashir')->character
        );
        // @codingStandardsIgnoreStart
        $this->assertSame(
            'http://startrektimelineswiki.com/w/images/thumb/d/d0/Changeling_Bashir_head.png/100px-Changeling_Bashir_head.png',
            $tmpl->byName('Changeling Bashir')->picture
        );
        // @codingStandardsIgnoreEnd
        $this->assertInstanceOf(Skills::class, $got[1]->skills);
        $this->assertSame(
            [
                'eng' => [250 + 96, 250 + 216],
                'med' => [648 + 99, 648 + 234],
                'sec' => [569 + 178, 569 + 423],
            ],
            $got[1]->skills->toArray()
        );
        $this->assertSame(
            [
                'changeling',
                'doctor',
                'dominion',
                'saboteur',
                'starfleet',
                'undercover operative',
            ],
            $got[1]->traits
        );
    }//end testParse()

    public function testTuvix()
    {
        $crew = $this->wiki->crew();
        $member = $crew->byName('Tuvix');
        $this->assertInstanceOf(CrewMember::class, $member);
        $this->assertSame('Tuvix', $member->name);
        $this->assertInstanceOf(Skills::class, $member->skills);
        $this->assertSame(4, $member->stars);
        $this->assertSame(
            [
                'dip' => [659 + 89, 659 + 228],
                'sci' => [230 + 137, 230 + 303],
                'sec' => [646 + 175, 646 + 356],
            ],
            $member->skills->toArray()
        );
        $this->assertSame(
            [
                'chef',
                'communicator',
                'federation',
                'mylean',
                'resourceful',
                'starfleet',
                'tactician',
                'talaxian',
                'telepath',
                'vulcan',
            ],
            $member->traits
        );
    }//end testTuvix()

    public function testChefNeelix()
    {
        $crew = $this->wiki->crew();
        $member = $crew->byName('Chef Neelix');
        $this->assertInstanceOf(CrewMember::class, $member);
        $this->assertSame('Chef Neelix', $member->name);
        $this->assertInstanceOf(Skills::class, $member->skills);
        $this->assertSame(1, $member->stars);
        $this->assertSame(
            [
                'dip' => [201 + 51, 201 + 189],
            ],
            $member->skills->toArray()
        );
        $this->assertSame(
            [
                'chef',
                'civilian',
                'communicator',
                'mylean',
                'resourceful',
                'survivalist',
                'talaxian',
            ],
            $member->traits
        );
    }//end testChefNeelix()

    public function testCMOPulaski()
    {
        $crew = $this->wiki->crew();
        $member = $crew->byName('CMO Pulaski');
        $this->assertInstanceOf(CrewMember::class, $member);
        $this->assertSame('CMO Pulaski', $member->name);
        $this->assertInstanceOf(Skills::class, $member->skills);
        $this->assertSame(3, $member->stars);
        $this->assertSame(
            [
                'med' => [460 + 80, 460 + 226],
                'sci' => [353 + 33, 353 + 97],
            ],
            $member->skills->toArray()
        );
        $this->assertSame(
            [
                'doctor',
                'federation',
                'human',
                'starfleet',
            ],
            $member->traits
        );
    }//end testCMOPulaski()

    public function testExport()
    {
        $crew = $this->wiki->crew()->export();
        $this->assertInternalType('array', $crew);
        $this->assertNotEmpty($crew);
        foreach ($crew as $member) {
            foreach ($member['skills'] as $name => $skill) {
                $this->assertInternalType(
                    'integer',
                    $skill,
                    "$member[name] skill '$name' should be integer"
                );
            }//end foreach
            foreach ($member['traits'] as $trait) {
                $this->assertInternalType(
                    'string',
                    $trait,
                    "$member[name] traits should be string"
                );
            }//end foreach
        }//end foreach
    }//end testExport()
}//end class
