<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-25
 * Time: 21:47
 */

namespace eidng8\Tests\Wiki\Templates\Triple;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Templates\Triple;

/**
 * ValidityTest
 */
class ValidityTest extends TestCase
{
    public function testSkillShortcut()
    {
        $text = '{{triple|dip| 106 | 350 | 805}}';
        $actual = Triple::load($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(['dip'], $actual->name());
        $this->assertSame(106, $actual->normal());
        $this->assertSame(350, $actual->elite());
        $this->assertSame(805, $actual->epic());
    }//end testNamed()

    public function testSkill()
    {
        $text = '{{triple|Diplomacy| 106 | 350 | 805}}';
        $actual = Triple::load($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(['diplomacy'], $actual->name());
        $this->assertSame(106, $actual->normal());
        $this->assertSame(350, $actual->elite());
        $this->assertSame(805, $actual->epic());
    }//end testNamed()

    public function testMultipleTraits()
    {
        $text
            = '{{triple|Quantum Mechanics|Prodigy|Jury Rigger|Undercover Operative| 17 | 41 | 34 |nobonus=all}}';
        $actual = Triple::load($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(
            [
                'quantum mechanics',
                'prodigy',
                'jury rigger',
                'undercover operative',
            ],
            $actual->name()
        );
        $this->assertSame(17, $actual->normal());
        $this->assertSame(41, $actual->elite());
        $this->assertSame(34, $actual->epic());
    }//end testMultipleNames()

    public function testIncomplete()
    {
        $text = ' {{triple|Exobiology| 26 | 86| |nobonus=all}}';
        $actual = Triple::load($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(['exobiology'], $actual->name());
        $this->assertSame(26, $actual->normal());
        $this->assertSame(86, $actual->elite());
        $this->assertSame(0, $actual->epic());
    }//end testIncomplete()

    public function testExtraneousValid()
    {
        $text = '{{currency|CRN}} {{triple| 4 | 4 | 8 }}';
        $actual = Triple::load($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(4, $actual->normal());
        $this->assertSame(4, $actual->elite());
        $this->assertSame(8, $actual->epic());
    }//end testExtraneousValid()

    public function testInvalid()
    {
        $text
            = <<<'EOT'
{{triple
   |nobonus=Saboteur,  Changeling
   |check=Jury Rigger, Crafty}}
EOT;
        $this->assertTrue(Triple::load($text)->isEmpty());
        new Triple($text);
    }//end testExtraneous()
}//end class
