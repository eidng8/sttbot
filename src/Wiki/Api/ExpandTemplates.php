<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-22
 * Time: 19:49
 */

namespace eidng8\Wiki\Api;

use eidng8\Traits\Wiki\Cache;
use eidng8\Traits\Wiki\Properties;

/**
 * The WikiMedia `query` action
 */
class ExpandTemplates
{
    use Properties, Cache;

    /**
     * The HTTP instance.
     *
     * @var Http
     */
    protected $api;

    /**
     * Wiki text returned from API
     *
     * @var string
     */
    protected $content;

    /**
     * Parse constructor.
     *
     * @param Http $api Http instance
     */
    public function __construct(Http $api)
    {
        $this->api = $api;
        $this->resetOptions();
    }//end __construct()

    /**
     * Reset to default options
     */
    public function resetOptions()
    {
        $this->options = [static::$PROP => ['wikitext']];
    }//end resetOptions()

    /**
     * Send the request
     *
     * @param string $text
     * @param bool   $fetch True to fetch new content.
     *
     * @return array|bool|string content returned from API, or `false` if error
     *                           occurred
     */
    public function get(string $text, bool $fetch = false): ?string
    {
        if (!$fetch && $this->content) {
            return $this->content;
        }

        $this->option('text', $text);
        $file = 'exptmpls/' . md5(serialize($this->optionsToParameters()));
        $this->content = $this->cache($file, [$this, 'fetch']);

        if (empty($this->content)) {
            return $this->content = null;
        }

        $this->content = $this->content['expandtemplates']['wikitext'];

        return $this->content;
    }//end get()

    /**
     * Calls the API endpoint
     *
     * @return mixed
     */
    public function fetch()
    {
        return $this->api->expandTemplates($this->optionsToParameters());
    }//end fetch()
}//end class
