<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-02
 * Time: 23:47
 */

namespace eidng8\Tests\Wiki\Templates;

use eidng8\Log\Log;
use eidng8\Tests\TestCase;
use eidng8\Wiki\Templates\MissionCost;

/**
 * MissionCostTest
 */
class MissionCostTest extends TestCase
{
    public function testTriple()
    {
        $text = '{{currency|CRN}} {{triple| 4 | 4 | 8 }}';
        $cost = new MissionCost($text);
        $this->assertTrue($cost->useChroniton());
        $this->assertFalse($cost->useTicket());
        $this->assertSame(4, $cost->normal());
        $this->assertSame(4, $cost->elite());
        $this->assertSame(8, $cost->epic());
    }//end testTriple()

    public function testSlashed()
    {
        $text = '{{currency|CRN}} 4 / 4 / 8';
        $cost = new MissionCost($text);
        $this->assertTrue($cost->useChroniton());
        $this->assertFalse($cost->useTicket());
        $this->assertSame(4, $cost->normal());
        $this->assertSame(4, $cost->elite());
        $this->assertSame(8, $cost->epic());
    }//end testSlashed()

    public function testTicket()
    {
        $text = '{{currency|tik}} 1';
        $cost = new MissionCost($text);
        $this->assertFalse($cost->useChroniton());
        $this->assertTrue($cost->useTicket());
        $this->assertSame(1, $cost->ticket());
    }//end testTicket()

    public function testEmpty()
    {
        Log::forTest();
        $this->assertNull(MissionCost::load(''));
        $this->assertTrue(
            Log::$testOutput->hasNoticeThatMatches('/Empty template/'),
            'Empty mission cost template should raise notice.'
        );
    }//end testEmpty()
}//end class
