<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2017-01-01
 * Time: 17:01
 */

namespace eidng8\Tests\Wiki\Api;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Api\ExpandTemplates;
use eidng8\Wiki\Api\Http;
use GuzzleHttp\Psr7\Response;
use Mockery;

/**
 * ExpandTemplatesTest
 */
class ExpandTemplatesTest extends TestCase
{
    // @codingStandardsIgnoreStart
    private $str = '{{:Episode 1 - The Art of War}}{{:Episode 2 - Never Forgive, Never Forget}}{{:Episode 3 - From On High}}{{:Episode 4 - Hostile Takeover}}{{:Episode 5 - Ethical Alternatives}}{{:Episode 6 - Something Rotten}}{{:Episode 7 - When Falcons Clash}}{{:Distress Calls - Delphic Expanse}}{{:Distress Calls - Celestial Temple}}';

    // @codingStandardsIgnoreEnd

    public function testGetCache()
    {
        $api = $this->newApiInstance();
        $fetched = $api->expandTemplates()->get($this->str);
        $mem = $api->expandTemplates()->get($this->str);
        $this->assertSame($fetched, $mem);
    }//end testGetCache()

    public function testGetNull()
    {
        /* @var ExpandTemplates $mock */
        $mock = Mockery::mock(
            ExpandTemplates::class . '[cache]',
            [Http::shouldRespond([])],
            ['cache' => null]
        );
        $this->assertNull($mock->get($this->str));
    }//end testGetNull()

    public function testFetch()
    {
        $content = file_get_contents(
            static::DIR_CACHE
            . '/exptmpls/94528e1052e8fde99572e90d70a78b4a.json'
        );

        /* @var ExpandTemplates $mock */
        $mock = Mockery::mock(
            ExpandTemplates::class . '[cacheRead]',
            [Http::shouldRespond([new Response(200, [], $content)])],
            ['cacheRead' => null]
        );
        $mock->cacheRoot(static::DIR_CACHE);
        $this->assertSame(
            json_decode($content, true)['expandtemplates']['wikitext'],
            $mock->get($this->str)
        );
    }//end testFetch()
}//end class
