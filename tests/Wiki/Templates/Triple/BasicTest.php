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
 * BasicTest
 */
class BasicTest extends TestCase
{
    /**
     * @expectedException \eidng8\Exceptions\EmptyTemplateException
     */
    public function testEmpty()
    {
        $text = '{{triple}}';
        new Triple($text);
    }//end testEmpty()

    public function testAllQuestionMarks()
    {
        $text = '{{triple|?|??}}';
        $this->assertTrue((new Triple($text))->isEmpty());
    }//end testAllQuestionMarks()

    public function testSimple()
    {
        $text = '{{triple| 4 | 4 | 8 }}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(4, $actual->normal());
        $this->assertSame(4, $actual->elite());
        $this->assertSame(8, $actual->epic());

        $actual = Triple::load($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(4, $actual->normal());
        $this->assertSame(4, $actual->elite());
        $this->assertSame(8, $actual->epic());
    }//end testSimple()

    public function testSimpleOmitFirst()
    {
        $text = '{{triple| | 4 | 8 }}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(0, $actual->normal());
        $this->assertSame(4, $actual->elite());
        $this->assertSame(8, $actual->epic());
    }//end testSimpleOmitFirst()

    public function testSimpleOmitSecond()
    {
        $text = '{{triple| 4 | | 8 }}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(4, $actual->normal());
        $this->assertSame(0, $actual->elite());
        $this->assertSame(8, $actual->epic());
    }//end testSimpleOmitSecond()

    public function testSimpleOmitThird()
    {
        $text = '{{triple| 4 | 4}}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(4, $actual->normal());
        $this->assertSame(4, $actual->elite());
        $this->assertSame(0, $actual->epic());
    }//end testSimpleOmitThird()

    public function testSimpleOmitFirstAndSecond()
    {
        $text = '{{triple|||8}}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(0, $actual->normal());
        $this->assertSame(0, $actual->elite());
        $this->assertSame(8, $actual->epic());
    }//end testSimpleOmitFirstAndSecond()

    public function testSimpleOmitFirstAndThird()
    {
        $text = '{{triple||4}}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(0, $actual->normal());
        $this->assertSame(4, $actual->elite());
        $this->assertSame(0, $actual->epic());
    }//end testSimpleOmitFirstAndThird()

    public function testSimpleOmitSecondAndThird()
    {
        $text = '{{triple|4}}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(4, $actual->normal());
        $this->assertSame(0, $actual->elite());
        $this->assertSame(0, $actual->epic());

        $text = '{{triple|4||}}';
        $actual = new Triple($text);
        $this->assertInstanceOf(Triple::class, $actual);
        $this->assertSame(4, $actual->normal());
        $this->assertSame(0, $actual->elite());
        $this->assertSame(0, $actual->epic());
    }//end testSimpleOmitSecondAndThird()
}//end class
