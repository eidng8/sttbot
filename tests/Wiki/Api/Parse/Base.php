<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 15:59
 */

namespace eidng8\Tests\Wiki\Api\Parse;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Api\Http;
use eidng8\Wiki\Api\Parse;
use GuzzleHttp\Psr7\Response;

/**
 * Parse test base
 */
class Base extends TestCase
{
    /**
     * test cache
     *
     * @var string
     */
    protected static $cacheFile;

    /**
     * Test subject
     *
     * @var Parse
     */
    protected $parse;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // make the cache "up to date"
        static::$cacheFile
            = static::DIR_CACHE .
            '/parse/Crew_1e8d43d73cc2f92192ca041f6ef6fcc7.json';
        touch(static::$cacheFile);
    }//end testGetCached()

    /**
     * {@inheritdoc}
     */
    protected function setUp(Http $http = null)
    {
        parent::setUp();

        if (!$http) {
            $http = Http::shouldRespond([new Response(200)]);
        }
        $this->parse = new Parse($http);
        $this->parse->cacheRoot(static::DIR_CACHE);
    }
}//end class
