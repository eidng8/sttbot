<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 16:04
 */

namespace eidng8\Tests\Traits\Wiki;

use eidng8\Tests\TestCase;
use eidng8\Traits\Wiki\Request;

/**
 * RequestTest
 */
class RequestTest extends TestCase
{
    use Request;

    public function testClearOptions()
    {
        $this->options = [1, 2, 3, 4, 5];
        $this->clearOptions();
        $this->assertSame([], $this->options);
    }//end testClearOptions()

    public function testHasOption()
    {
        $this->options = ['p' => 'v', 'a' => 'b'];
        $this->assertTrue($this->hasOption('p'));
        $this->assertTrue($this->hasOption('a'));
        $this->assertFalse($this->hasOption('c'));
    }//end testHasOptions()

    public function testRemoveOption()
    {
        $this->options = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
        $this->removeOption('a');
        $this->assertSame(['b' => 2, 'c' => 3, 'd' => 4], $this->options);
    }//end testRemoveOption()

    public function testGetOption()
    {
        $this->options = ['p' => 'v', 'a' => 'b'];
        $this->assertSame('v', $this->option('p'));
        $this->assertSame('b', $this->option('a'));
    }//end testGetOptions()

    public function testSetOption()
    {
        $this->options = ['p' => 'v', 'a' => 'b'];
        $this->assertSame('b', $this->option('a', 'c'));
        $this->assertSame('c', $this->option('a', 'd'));
        $this->assertSame('d', $this->option('a'));
    }//end testSetOptions()

    public function testGetOptions()
    {
        $expected = ['p' => 'v', 'a' => 'b'];
        $this->options = $expected;
        $this->assertSame($expected, $this->options());
    }//end testGetOptions()

    public function testSetOptions()
    {
        $start = ['p' => 'v', 'a' => 'b'];
        $final = ['c', 'd', 'e'];
        $this->options = $start;
        $this->assertSame($start, $this->options($final));
        $this->assertSame($final, $this->options());
    }//end testSetOptions()

    // Not possible with parameter type declarations
    // public function testSetOptionsNotArray()
    // {
    //     $expected = ['p' => 'v', 'a' => 'b'];
    //     $this->options = $expected;
    //     $this->assertSame($expected, $this->options('p'));
    //     $this->assertSame(['p'], $this->options());
    // }//end testSetOptionsNotArray()

    public function testSetOptionsMerge()
    {
        $start = ['p' => 'v', 'a' => 'b'];
        $merger = ['d' => 'e', 'f' => 'g'];
        $final = ['p' => 'v', 'a' => 'b', 'd' => 'e', 'f' => 'g'];
        $this->options = $start;
        $this->assertSame($start, $this->options($merger, true));
        $this->assertSame($final, $this->options());
    }//end testSetOptionsMerge()

    public function testOptionsToParameters()
    {
        $input = ['a' => 'b', 'c' => ['d', 'e'], 'f' => 'g'];
        $expected = ['a' => 'b', 'c' => 'd|e', 'f' => 'g'];
        $this->options = $input;
        $this->assertSame($expected, $this->optionsToParameters());
    }//end testOptionsToParameters()
}//end class
