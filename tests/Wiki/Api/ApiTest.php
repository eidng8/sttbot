<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 14:36
 */

namespace eidng8\Tests\Wiki\Api;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Api\Api;
use eidng8\Wiki\Api\Http;
use eidng8\Wiki\Api\Parse;
use eidng8\Wiki\Api\Query;

/**
 * ApiTest
 */
class ApiTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertNotNull(new Api(new Http('url'), static::DIR_CACHE));
    }//end testConstruct()

    public function testParse()
    {
        $this->assertInstanceOf(
            Parse::class,
            (new Api(new Http('url'), static::DIR_CACHE))->parse()
        );
    }//end testParse()

    public function testParseForceRecreate()
    {
        $api = new Api(new Http('url'), static::DIR_CACHE);
        $inst = $api->parse();
        $this->assertSame($inst, $api->parse());
        $this->assertNotSame($inst, $api->parse(true));
    }//end testParseForceRecreate()

    public function testQuery()
    {
        $this->assertInstanceOf(
            Query::class,
            (new Api(new Http('url'), static::DIR_CACHE))->query()
        );
    }//end testQuery()

    public function testQueryForceRecreate()
    {
        $api = new Api(new Http('url'), static::DIR_CACHE);
        $inst = $api->query();
        $this->assertSame($inst, $api->query());
        $this->assertNotSame($inst, $api->query(true));
    }//end testQueryForceRecreate()
}//end class
