<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 16:04
 */

namespace eidng8\Tests\Traits\Wiki;

use eidng8\Tests\TestCase;
use eidng8\Traits\Wiki\Cache;

/**
 * CacheTest
 */
class CacheTest extends TestCase
{
    use Cache;

    private $file = 'some/file';

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        if (file_exists(static::DIR_CACHE . '/some/file.json')) {
            unlink(static::DIR_CACHE . '/some/file.json');
        }
        if (file_exists(static::DIR_CACHE . '/some')) {
            rmdir(static::DIR_CACHE . '/some');
        }
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->removeFile($this->file);
    }//end testCacheRoot()

    protected function setUp()
    {
        parent::setUp();
        $this->cacheRoot = static::DIR_CACHE;
        $this->removeFile($this->file, true);
    }//end testGetCacheNotExist()

    public function testGetCacheRoot()
    {
        $this->assertSame($this->cacheRoot, $this->cacheRoot());
    }//end testGetCache()

    public function testSetCacheRoot()
    {
        $this->assertSame($this->cacheRoot, $this->cacheRoot('somewhere'));
        $this->assertSame('somewhere', $this->cacheRoot());
    }//end testGetCacheNotExist()

    public function testGetCache()
    {
        touch(
            static::DIR_CACHE .
            '/parse/Crew_1e8d43d73cc2f92192ca041f6ef6fcc7.json'
        );
        $this->assertNotFalse(
            $this->cache('parse/Crew_1e8d43d73cc2f92192ca041f6ef6fcc7')
        );
    }//end removeFile()

    public function testGetCacheNotExist()
    {
        $file = "{$this->cacheRoot}/nothing";
        if (is_file($file)) {
            unlink($file);
        }
        $this->assertFalse($this->cache('nothing'));
    }//end testCheckDirCreate()

    public function testCacheFetchedEmpty()
    {
        $this->assertNull(
            $this->cache(
                $this->file,
                function () {
                    return null;
                }
            )
        );
    }//end testSetCache()

    public function testCheckDirCreate()
    {
        $file = "{$this->cacheRoot}/to_be_del/nothing";
        $dir = dirname($file);
        if (is_file($file)) {
            unlink($file);
        }
        if (is_dir($dir)) {
            rmdir($dir);
        }
        $this->cacheCheckDir($file);
        $this->assertTrue(is_dir($dir));
        rmdir($dir);
    }

    public function testSetCache()
    {
        $this->cache(
            $this->file,
            function () {
                return 'just a test';
            }
        );
        $this->assertSame('just a test', $this->cache($this->file));
    }

    /**
     * Removes the specified temporary file
     *
     * @param      $file
     * @param bool $dir
     */
    private function removeFile($file, $dir = false)
    {
        $path = "{$this->cacheRoot}/$file.json";
        if (is_file($path)) {
            unlink($path);
            if ($dir) {
                $dirname = dirname($path);
                rmdir($dirname);
            }
        }
    }//end testSetCacheRoot()
}//end class
