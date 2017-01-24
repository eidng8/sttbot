<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 16:04
 */

namespace eidng8\Tests\Traits\Wiki;

use eidng8\Tests\TestCase;
use eidng8\Traits\Wiki\Properties;

/**
 * PropertiesTest
 */
class PropertiesTest extends TestCase
{
    use Properties;

    public function testAddProperty()
    {
        $this->addProperty('test-prop');
        $this->assertTrue($this->hasProperty('test-prop'));

        $this->options([static::$PROP => 'test-prop']);
        $this->assertTrue($this->hasProperty('test-prop'));
        $this->addProperty('test-val');
        $this->assertTrue($this->hasProperty('test-val'));
    }//end hasProperty()

    public function testRemovePropertyValue()
    {
        $this->options['prop'] = 'test-prop';
        $this->assertTrue($this->hasProperty('test-prop'));
        $this->removeProperty('test-prop');
        $this->assertFalse($this->hasProperty('test-prop'));
    }//end testRemovePropertyValue()

    public function testRemovePropertyArray()
    {
        $this->removeProperty('test-prop');
        $this->addProperty('test-prop');
        $this->addProperty('another');
        $this->removeProperty('test-prop');
        $this->assertFalse($this->hasProperty('test-prop'));
        $this->assertTrue($this->hasProperty('another'));
    }//end testRemovePropertyArray()

    public function testProperties()
    {
        $this->addProperty('test-prop');
        $this->assertSame(['test-prop'], $this->properties(['a', 'b', 'c']));
        $this->assertSame(['a', 'b', 'c'], $this->properties());
    }//end testProperties()
}//end class
