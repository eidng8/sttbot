<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 15:59
 */

namespace eidng8\Tests\Wiki\Api;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Api\Http;
use eidng8\Wiki\Api\Query;
use GuzzleHttp\Psr7\Response;

/**
 * QueryTest
 */
class QueryTest extends TestCase
{

    /**
     * Test subject
     *
     * @var Query
     */
    private $query;

    /**
     * {@inheritdoc}
     */
    protected function setUp(Http $http = null)
    {
        parent::setUp();
        if (!$http) {
            $http = Http::shouldRespond([new Response(400)]);
        }
        $this->query = new Query($http);
        $this->query->cacheRoot(static::DIR_CACHE);
    }

    public function testTitles()
    {
        $this->query->titles([]);
        $this->query->titles(['title']);
        $this->assertSame(['title'], $this->query->option(Query::$TITLES));
    }//end testTitles()

    public function testThumbnails()
    {
        $http = Http::shouldRespond(
            [
                new Response(
                    200,
                    [],
                    file_get_contents(static::DIR_SAMPLE . '/thumbnails.json')
                ),
            ]
        );
        $query = new Query($http);
        $actual = $query->thumbnails(
            ['"Dark Ages" McCoy', 'Changeling Bashir']
        );
        $this->assertTrue(is_string($actual['"Dark Ages" McCoy']));
        $this->assertContains('McCoy', $actual['"Dark Ages" McCoy']);
        $this->assertTrue(is_string($actual['Changeling Bashir']));
        $this->assertContains('Bashir', $actual['Changeling Bashir']);
        $this->assertSame($query->get(), $query->get());
    }//end testThumbnails()

    public function testThumbnailsWithEmptyTitle()
    {
        $this->assertSame([], $this->query->thumbnails([]));
    }//end testThumbnailsEmpty()

    public function testThumbnailsGotNull()
    {
        $query = new Query(Http::shouldRespond([new Response(200, [], '')]));
        $this->assertEmpty($query->thumbnails(['nothing']));
    }//end testGetThumbnailsGotNull()
}//end class
