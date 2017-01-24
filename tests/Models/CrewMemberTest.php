<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-06
 * Time: 17:26
 */

namespace eidng8\Tests\Models;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Models\CrewMember;

/**
 * CrewMemberTest
 */
class CrewMemberTest extends TestCase
{
    /**
     * test data
     *
     * @var array
     */
    private static $data;

    /**
     * test subject
     *
     * @var CrewMember
     */
    private static $model;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$data = json_decode(
            file_get_contents(static::DIR_SAMPLE . '/changeling-bashir.json'),
            true
        );

        static::$model = new CrewMember(static::$data);

        // static::$model['eng'] = static::$model['skills']->eng;
        // static::$model['med'] = static::$model['skills']->med;
        // static::$model['sec'] = static::$model['skills']->sec;
        // unset(static::$model['skills']);
        //
        // foreach ($model as $index => $item) {
        //     static::$model[$index] = $item;
        // }//end foreach
    }

    public function testModel()
    {
        $this->assertSame('Changeling Bashir', static::$model['name']);
        $this->assertSame('Changeling Bashir', static::$model['CrewName']);
        $this->assertSame('Changeling%20Bashir', static::$model->uri());
        $this->assertSame('Changeling%20Bashir', static::$model->page());

        $this->assertSame('Changeling', static::$model['character']);
        $this->assertSame('Changeling', static::$model['CharName']);
        $this->assertSame(
            'Changeling%20%28Character%29',
            static::$model['charpage']
        );

        $this->assertSame(
            'http://startrektimelineswiki.com/w/images/thumb/d/d0'
            . '/Changeling_Bashir_head.png/100px-Changeling_Bashir_head.png',
            static::$model->thumbnail()
        );

        $this->assertSame('Changeling', static::$model['race']);

        $this->assertEquals(21, static::$model['eng']);
        $this->assertEquals(39, static::$model['med']);
        $this->assertEquals(57, static::$model['sec']);
        $this->assertEquals(4, static::$model['stars']);

        $this->assertSame(
            [
                'Changeling',
                'Dominion',
                'Starfleet',
                'Doctor',
                'Undercover Operative',
                'Saboteur}}',
            ],
            static::$model['traits']
        );
        $this->assertFalse(static::$model->hasTraits(['does not exist']));
        $this->assertFalse(static::$model->hasTraits([]));
        $this->assertFalse(static::$model->hasTraits(null));

        static::$model->setRating(123);
        $this->assertSame(123, static::$model->getRating());
    }//end testModel()

    public function testToArray()
    {
        $this->assertEquals(static::$data, static::$model->toArray());
    }//end testToArray()

    public function testUnset()
    {
        $model = new CrewMember(static::$data);
        $this->assertNull($model['nothing']);

        unset($model['CrewName']);
        $this->assertTrue(isset($model['CrewName']));
        $this->assertNull($model['CrewName']);

        unset($model['CharName']);
        $this->assertTrue(isset($model['CharName']));
        $this->assertNull($model['CharName']);

        unset($model['ENG']);
        $this->assertTrue(isset($model['eng']));
        $this->assertNull($model['eng']);

        unset($model['STARS']);
        $this->assertTrue(isset($model['stars']));
        $this->assertNull($model['STARS']);
    }//end testUnset()

    public function testJsonEncode()
    {
        $this->assertSame(
            json_encode(static::$data, JSON_NUMERIC_CHECK),
            json_encode(static::$model, JSON_NUMERIC_CHECK)
        );
    }//end testJsonEncode()
}//end class
