<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-22
 * Time: 21:36
 */

namespace eidng8\Traits\Wiki;

use eidng8\Log\Log;

define(
    'JSON_OPTIONS',
    JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE |
    JSON_PRESERVE_ZERO_FRACTION
);

/**
 * File cache of API requests
 */
trait Cache
{
    /**
     * Directory to store cache files
     *
     * @var string
     */
    protected $cacheRoot = 'cache';

    /**
     * Expiry of cached files, in seconds
     *
     * @var int
     */
    protected $ttl = 3600 * 24;

    /**
     * Get or set the cache directory
     *
     * @param string $root omit to retrieve current cache directory, or path
     *                     string to set the cache directory
     *
     * @return string Returns current cache directory if `$root` parameter is
     *                null; otherwise returns nothing
     */
    public function cacheRoot($root = null): string
    {
        if (null === $root) {
            return $this->cacheRoot;
        }

        $old = $this->cacheRoot;
        $this->cacheRoot = $root;

        return $old;
    }//end cacheRoot()

    /**
     * Retrieves the specified cache content; if it's not cached, then calls
     * the `$fetch` to retrieve content, cache and returns it.
     *
     * The return values means:
     *  * `false` if `$fetch` is not provided and cache is not found;
     *  * `null` if `$fetch` is provided and `$fetch` returns empty content;
     *  * otherwise return cached or `$fetch`ed content.
     *
     * @param string   $file  the cache file to be retrieved or written
     * @param callable $fetch omit to retrieve the cached content, or set to
     *                        the new content to be written to cache
     *
     * @return mixed|false|null
     */
    public function cache(string $file, callable $fetch = null)
    {
        $path = "{$this->cacheRoot}/$file.json";
        Log::info("get file $file");

        $cached = $this->cacheRead($path);
        if ($cached) {
            Log::debug('cache hit', ['file' => $file]);
            return $cached;
        }

        if (null === $fetch) {
            Log::debug('no fetcher', ['file' => $file]);
            return false;
        }

        $this->cacheCheckDir($path);

        Log::debug('attempting to fetch from network...', ['file' => $file]);
        if (empty($content = $fetch())) {
            Log::debug('fetch failed', ['file' => $file]);
            return null;
        }

        Log::debug('file fetched', ['file' => $file]);
        $this->cacheWrite($path, $content);
        Log::debug('cache saved', ['file' => $file]);

        return $content;
    }//end cache()

    /**
     * Reads the specified cache
     *
     * @param string $path
     *
     * @return mixed
     */
    public function cacheRead(string $path)
    {
        if (!$this->cacheCheck($path) ||
            !($content = file_get_contents($path))
        ) {
            return null;
        }

        return json_decode($content, true);
    }//end cacheCheckDir()

    /**
     * Check if the specified cache exists and is valid
     *
     * @param string $path
     *
     * @return bool
     */
    public function cacheCheck(string $path): bool
    {
        return is_file($path) && filemtime($path) + $this->ttl > time();
    }//end cacheRead()

    /**
     * Make sure the specified cache directory exists
     *
     * @param string $path
     */
    public function cacheCheckDir(string $path)
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }//end cacheCheck()

    /**
     * Write the given content to cache
     *
     * @param string $path
     * @param mixed  $content
     *
     * @return int
     */
    public function cacheWrite(string $path, $content): int
    {
        return file_put_contents(
            $path,
            json_encode(
                $content,
                JSON_OPTIONS | JSON_PRETTY_PRINT
            )
        );
    }//end cacheWrite()

    /**
     * Generate a file name for the given page
     *
     * @param string $page
     * @param array  $options
     *
     * @return string
     */
    public function cacheFileName(string $page, array $options): string
    {
        return preg_replace('#[\s<>:"/\|?*]#', '_', $page)
               . '_' . md5(serialize($options));
    }//end fileName()
}//end trait
