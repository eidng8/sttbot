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
     * test cache
     *
     * @var string
     */
    private static $cacheFile;

    /**
     * Test subject
     *
     * @var Query
     */
    private $query;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$cacheFile = static::DIR_CACHE
                             . '/query/2bc2554e05cf988e81f73d138cc51212.json';
        touch(static::$cacheFile);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(Http $http = null)
    {
        parent::setUp();

        if (!$http) {
            $http = Http::shouldRespond([new Response(200)]);
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
        // cache/query/36d83a95eae2f5574983568936f15ce9.json
        $actual = $this->query->thumbnails(
            ['"Dark Ages" McCoy', 'Changeling Bashir']
        );
        $this->assertTrue(is_string($actual['"Dark Ages" McCoy']));
        $this->assertContains('McCoy', $actual['"Dark Ages" McCoy']);
        $this->assertTrue(is_string($actual['Changeling Bashir']));
        $this->assertContains('Bashir', $actual['Changeling Bashir']);
        $this->assertSame($this->query->get(), $this->query->get());
    }//end testThumbnails()

    public function testThumbnailsWithEmptyTitle()
    {
        $this->assertSame([], $this->query->thumbnails([]));
    }//end testThumbnailsEmpty()

    public function testThumbnailsGotNull()
    {
        // cache/query/2bc2554e05cf988e81f73d138cc51212.json
        $this->assertEmpty($this->query->thumbnails(['nothing']));
    }//end testGetThumbnailsGotNull()
}//end class
