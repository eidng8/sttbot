<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 15:35
 */

namespace eidng8\Tests;

use eidng8\Log\Log;
use eidng8\Wiki;
use eidng8\Wiki\Api\Api;
use eidng8\Wiki\Api\Http;
use Monolog\Logger;
use PHPUnit\Framework\TestCase as TestCaseBase;

/**
 * TestCase
 */
class TestCase extends TestCaseBase
{
    /**
     * Unit test cache directory
     *
     * @var string
     */
    public const DIR_CACHE = __DIR__ . '/data/cache';

    /**
     * Unit test data directory
     *
     * @var string
     */
    public const DIR_DATA = __DIR__ . '/data';

    /**
     * Unit test sample data directory
     *
     * @var string
     */
    public const DIR_SAMPLE = __DIR__ . '/data/samples';

    /**
     * Unit tests base path
     *
     * @var string
     */
    public const PATH = __DIR__;

    protected static $streamError;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Log::setLevel(Logger::DEBUG);
        Log::forTest();
    }

    public function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }//end tearDown()

    /**
     * @return Wiki
     */
    protected function newWikiInstance(): Wiki
    {
        $api = $this->newApiInstance();

        return new Wiki($api->parse(), $api->query(), $api->expandTemplates());
    }//end newApiInstance()

    /**
     * @return Api
     */
    protected function newApiInstance(): Api
    {
        return new Api(
            new Http('http://startrektimelineswiki.com/w/api.php'),
            static::DIR_CACHE
        );
    }//end newApiInstance()

    /**
     * Check if two array has same set of keys
     *
     * @param array $expected
     * @param array $actual
     *
     * @return void
     */
    protected function checkArrayKeys(array $expected, array $actual)
    {
        $keys = array_keys($actual);
        sort($keys);
        $expk = $expected;
        sort($expk);
        $this->assertSame($keys, $expk);
    }//end checkArrayKeys()
}//enc class
