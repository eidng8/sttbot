<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 15:59
 */

namespace eidng8\Tests\Wiki\Api\Parse;

/**
 * MapTest
 */
class MapTest extends Base
{
    public function testMapLinks()
    {
        $this->assertSame(
            ['Credits' => true, 'Dilithium' => true],
            $this->parse->mapLinks(
                [
                    ['exists' => '', '*' => 'Credits'],
                    ['exists' => '', '*' => 'Dilithium'],
                ]
            )
        );
    }//end testMapLinks()

    public function testMapImages()
    {
        $this->assertSame(
            [
                'Credits_sm.png'   => true,
                'Dilithium_sm.png' => true,
                'CMD.png'          => true,
            ],
            $this->parse->mapImages(
                [
                    'Credits_sm.png',
                    'Dilithium_sm.png',
                    'CMD.png',
                ]
            )
        );
    }//end testmapImages()

    public function testMapTemplates()
    {
        $this->assertSame(
            ['Template:Currency' => true, 'Template:Skill' => true],
            $this->parse->mapTemplates(
                [
                    [
                        'ns'     => 10,
                        'exists' => '',
                        '*'      => 'Template:Currency',
                    ],
                    [
                        'ns'     => 10,
                        'exists' => '',
                        '*'      => 'Template:Skill',
                    ],
                ]
            )
        );
    }//end testMapTemplates()
}//end class
