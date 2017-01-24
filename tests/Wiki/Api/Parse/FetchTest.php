<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 15:59
 */

namespace eidng8\Tests\Wiki\Api\Parse;

use eidng8\Wiki\Api\Http;
use eidng8\Wiki\Api\Parse;
use GuzzleHttp\Psr7\Response;
use Mockery;

/**
 * FetchTest
 */
class FetchTest extends Base
{
    public function testGetCached()
    {
        $parse = Mockery::mock(
            Parse::class . '[]',
            [Http::shouldRespond([new Response(200)])]
        );
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $parse->shouldNotReceive('fetch');

        /* @var Parse $parse */
        $parse->cacheRoot(static::DIR_CACHE);
        $parse->page('Crew', 9);
        $actual = $parse->get();
        $this->assertSame('Crew', $actual['title']);
        $this->assertSame(66, $actual['pageid']);
        $this->assertTrue($actual['links']['"Dark Ages" McCoy']);
        $this->assertTrue($actual['links']['Changeling (Character)']);
        $this->assertTrue($actual['templates']['Template:Skill']);
        $this->assertTrue($actual['templates']['Template:MPCrewList']);
        $this->assertTrue($actual['images']['Changeling_Bashir_head.png']);
        $this->assertSame($parse->get(), $actual);
    }

    public function testGetFetch()
    {
        $this->setUp(
            Http::shouldRespond(
                [new Response(200, [], file_get_contents(static::$cacheFile))]
            )
        );
        $parse = Mockery::mock(
            Parse::class . '[fetch]',
            [
                Http::shouldRespond(
                    [
                        new Response(
                            200,
                            [],
                            '{"parse":' . file_get_contents(static::$cacheFile)
                            . '}'
                        ),
                    ]
                ),
            ]
        );
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $parse->shouldReceive('fetch')->passthru();

        /* @var Parse $parse */
        $parse->cacheRoot(static::DIR_CACHE . '/not-exist');
        $parse->page('Crew', 9);
        $actual = $parse->get();
        $this->assertSame('Crew', $actual['title']);
        $this->assertSame(66, $actual['pageid']);
        $this->assertTrue($actual['links']['"Dark Ages" McCoy']);
        $this->assertTrue($actual['links']['Changeling (Character)']);
        $this->assertTrue($actual['templates']['Template:Skill']);
        $this->assertTrue($actual['templates']['Template:MPCrewList']);
        $this->assertTrue($actual['images']['Changeling_Bashir_head.png']);
        $this->assertSame($parse->get(), $actual);

        unlink(
            static::DIR_CACHE .
            '/not-exist/parse/Crew_1e8d43d73cc2f92192ca041f6ef6fcc7.json'
        );
        rmdir(static::DIR_CACHE . '/not-exist/parse');
        rmdir(static::DIR_CACHE . '/not-exist');
    }//end testGetCached()

    public function testGetNothing()
    {
        $this->parse->option(Parse::$PAGE, 'nothing');
        $this->assertNull($this->parse->get());
    }//end testGetFetch()
}//end class
