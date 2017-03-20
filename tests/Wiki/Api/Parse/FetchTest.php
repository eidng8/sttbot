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
    private $page = "test-get-fetched";

    private $path;

    protected function setUp(Http $http = null)
    {
        parent::setUp($http);
        $this->path = static::DIR_CACHE .
                      "/parse/{$this->page}_7a73fff6ff41f1c59683aec0d2e7597e.json";
        file_put_contents(
            $this->path,
            json_encode(
                [
                    'title'     => 'a title',
                    'links'     => [['exists' => true, '*' => 'a link']],
                    'images'    => ['img'],
                    'templates' => [['exists' => true, '*' => 'a template']],
                    'wikitext'  => ['*' => 'some text'],
                ]
            )
        );
    }

    public function tearDown()
    {
        parent::tearDown();
        unlink($this->path);
    }

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
        $parse->page($this->page);
        $actual = $parse->get();
        $this->assertSame('a title', $actual['title']);
        $this->assertTrue($actual['links']['a link']);
        $this->assertTrue($actual['templates']['a template']);
        $this->assertTrue($actual['images']['img']);
        $this->assertSame($parse->get(), $actual);
    }

    public function testGetNothing()
    {
        $this->parse->option(Parse::$PAGE, 'nothing');
        $this->assertNull($this->parse->get());
    }//end testGetFetch()
}//end class
