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
use PHPUnit_Framework_TestCase;

/**
 * TestCase
 */
class TestCase extends PHPUnit_Framework_TestCase
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
}//enc class
