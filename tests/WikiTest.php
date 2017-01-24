<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-10
 * Time: 08:39
 */

namespace eidng8\Tests;

use eidng8\Wiki\Models\CrewMember;
use eidng8\Wiki\Models\Mission;
use eidng8\Wiki\Templates\CrewList;
use eidng8\Wiki\Templates\MissionList;

/**
 * WikiTest
 */
class WikiTest extends TestCase
{
    public function testCrew()
    {
        $wiki = $this->newWikiInstance();
        $crew = $wiki->crew();
        $this->assertSame($crew, $wiki->crew());
        $this->assertInstanceOf(CrewList::class, $crew);
        $this->assertTrue(is_array($crew->get()));
        $this->assertInstanceOf(CrewMember::class, current($crew->get()));
    }//end testCrew()

    public function testMissions()
    {
        $wiki = $this->newWikiInstance();
        $missions = $wiki->missions();
        $this->assertSame($missions, $wiki->missions());
        $this->assertInstanceOf(MissionList::class, $missions);
        $this->assertTrue(is_array($missions->get()));
        $this->assertNotEmpty($missions->get());
        $this->assertTrue(is_array(current($missions->get())));
        $this->assertNotEmpty(current($missions->get()));
        $this->assertTrue(is_array(current(current($missions->get()))));
        $this->assertInstanceOf(
            Mission::class,
            current(current(current($missions->get())))
        );
    }//end testMissions()
}//end class
