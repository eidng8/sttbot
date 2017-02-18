<?php
/*
 * @author  eidng8
 * @license https://creativecommons.org/licenses/by-sa/4.0/
 * @link    https://github.com/eidng8/stthb
 */

namespace eidng8\Tests\Wiki\Models;

use eidng8\Wiki\Models\Skills;

class SkillsTest extends \PHPUnit_Framework_TestCase
{
    public function testOffsetExists()
    {
        $skills = new Skills();
        $this->assertTrue(empty($skills['not exist']));
    }//end testOffsetExists()

    public function testOffsetGet()
    {
        $skills = new Skills();
        $this->assertNull($skills['not exist']);
    }//end testOffsetExists()
}//end class
