<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 15:59
 */

namespace eidng8\Tests\Wiki\Api\Parse;

use eidng8\Wiki\Api\Parse;

/**
 * BasicTest
 */
class BasicTest extends Base
{
    public function testFollowRedirects()
    {
        $this->parse->followRedirects();
        $this->assertTrue($this->parse->hasOption(Parse::$REDIRECTS));
        $this->parse->followRedirects(false);
        $this->assertFalse($this->parse->hasOption(Parse::$REDIRECTS));
        $this->parse->followRedirects(true);
        $this->assertTrue($this->parse->hasOption(Parse::$REDIRECTS));
    }//end testFollowRedirects()

    public function testToc()
    {
        $this->parse->toc();
        $this->assertTrue($this->parse->hasOption(Parse::$DISABLETOC));
        $this->parse->toc(true);
        $this->assertFalse($this->parse->hasOption(Parse::$DISABLETOC));
        $this->parse->toc(false);
        $this->assertTrue($this->parse->hasOption(Parse::$DISABLETOC));
    }//end testToc()

    public function testPage()
    {
        $this->parse->page('some page');
        $this->assertSame('some page', $this->parse->option(Parse::$PAGE));
    }//end testPage()

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage "page" cannot be used with "title" and "text"
     */
    public function testPageException()
    {
        $this->parse->text('some test', 'some title');
        $this->parse->page('some page');
    }//end testPageException()

    public function testSection()
    {
        $this->parse->section(1);
        $this->assertSame(1, $this->parse->option(Parse::$SECTION));
    }//end testSection()

    public function testText()
    {
        $this->parse->text('some text', 'a title');
        $this->assertSame('some text', $this->parse->option(Parse::$TEXT));
        $this->assertSame('a title', $this->parse->option(Parse::$TITLE));
    }//end testText()

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage "title" and "text" cannot be used with "page"
     */
    public function testTextException()
    {
        $this->parse->page('a page');
        $this->parse->text('some text', 'a title');
    }//end testTextException()

    public function testProperties()
    {
        $this->parse->properties(['a']);
        $this->assertTrue($this->parse->hasProperty('a'));
    }//end testProperties()
}//end class
