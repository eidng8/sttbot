<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-22
 * Time: 19:49
 */

namespace eidng8\Wiki\Api;

use eidng8\Traits\Wiki\Cache;
use eidng8\Traits\Wiki\Extractor\TableExtractor;
use eidng8\Traits\Wiki\Properties;

/**
 * The WikiMedia `parse` action
 */
class Parse
{
    use Properties {
        properties as traitProperties;
    }

    use Cache, TableExtractor;

    /**
     * `redirects` option
     *
     * @var string
     */
    public static $REDIRECTS = 'redirects';

    /**
     * `disabletoc` options
     *
     * @var string
     */
    public static $DISABLETOC = 'disabletoc';

    /**
     * `page` options
     *
     * @var string
     */
    public static $PAGE = 'page';

    /**
     * `section` options
     *
     * @var string
     */
    public static $SECTION = 'section';

    /**
     * `text` options
     *
     * @var string
     */
    public static $TEXT = 'text';

    /**
     * `title` options
     *
     * @var string
     */
    public static $TITLE = 'title';

    /**
     * The HTTP instance.
     *
     * @var Http
     */
    protected $api;

    /**
     * Wiki text returned from API
     *
     * @var array
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
    public function resetOptions(): void
    {
        // merge these 2 statements causes PHPUnit reporting uncovered line
        $this->options = [
            static::$DISABLETOC => '',
            static::$REDIRECTS  => '',
        ];
        $this->options[static::$PROP] = [
            'wikitext',
            'templates',
            'links',
            'images',
        ];
    }//end resetOptions()

    /**
     * If a page is set to a redirect, resolve it
     *
     * @param bool $follow
     */
    public function followRedirects(bool $follow = true): void
    {
        if ($follow) {
            $this->options[static::$REDIRECTS] = '';
        } elseif ($this->hasOption(static::$REDIRECTS)) {
            unset($this->options[static::$REDIRECTS]);
        }
    }//end followRedirects()

    /**
     * Enable table of contents in output
     *
     * @param bool $enabled
     */
    public function toc(bool $enabled = false): void
    {
        if (!$enabled) {
            $this->options[static::$DISABLETOC] = '';
        } elseif ($this->hasOption(static::$DISABLETOC)) {
            unset($this->options[static::$DISABLETOC]);
        }
    }//end toc()

    /**
     * The page to be retrieved
     *
     * @param string $page
     * @param int    $section
     */
    public function page(string $page, int $section = null): void
    {
        if ($this->hasOption(static::$TEXT) ||
            $this->hasOption(static::$TITLE)
        ) {
            throw new \RuntimeException(
                'property "page" cannot be used with "title" and "text"'
            );
        }

        $this->option(static::$PAGE, $page);
        if (null !== $section) {
            $this->section($section);
        }
    }//end page()

    /**
     * Section to be retrieved
     *
     * @param int $section
     */
    public function section(int $section): void
    {
        $this->option(static::$SECTION, $section);
    }//end section()

    /**
     * Wiki text to parse
     *
     * @param string $text  Wiki text to parse
     * @param string $title title to use
     */
    public function text(string $text, string $title = null): void
    {
        if ($this->hasOption(static::$PAGE)) {
            throw new \RuntimeException(
                'property "title" and "text" cannot be used with "page"'
            );
        }
        $this->option(static::$TEXT, $text);
        $this->option(static::$TITLE, $title);
    }//end text()

    /**
     * {@inheritdoc}
     */
    public function properties(
        array $properties = ['wikitext', 'templates', 'links', 'images']
    ): array {
        return $this->traitProperties($properties);
    }//end properties()

    /**
     * Send the request
     *
     * @param bool $fetch True to fetch new content.
     *
     * @return array content returned from API
     */
    public function get(bool $fetch = false): ?array
    {
        if (!$fetch && $this->content) {
            return $this->content;
        }

        $file = $this->cacheFileName(
            $this->option(static::$PAGE),
            $this->optionsToParameters()
        );
        $file = "parse/$file";
        $this->content = $this->cache($file, [$this, 'fetch']);

        if (empty($this->content)) {
            return $this->content = null;
        }

        $this->content['links'] = $this->mapLinks($this->content['links']);
        $this->content['images'] = $this->mapImages($this->content['images']);
        $this->content['templates']
            = $this->mapTemplates($this->content['templates']);

        $this->content['wikitext']['*'] = $this->interpolate(
            $this->content['wikitext']['*'],
            ['pagename' => $this->option(static::$PAGE)]
        );
        $this->wikiText = $this->content['wikitext']['*'];

        return $this->content;
    }//end get()

    /**
     * Flatten links array, with the page name as keys
     *
     * @param array $wikiLinks
     *
     * @return string[]
     */
    public function mapLinks(array $wikiLinks): array
    {
        $flattened = [];
        foreach ($wikiLinks as $wikiLink) {
            if (array_key_exists('exists', $wikiLink)) {
                $flattened[$wikiLink['*']] = true;
            }
        }//end foreach

        return $flattened;
    }//end fetch()

    /**
     * Flatten images array, with the page name as keys
     *
     * @param array $images
     *
     * @return string[]
     */
    public function mapImages(array $images): array
    {
        $flattened = [];
        foreach ($images as $image) {
            $flattened[$image] = true;
        }//end foreach

        return $flattened;
    }//end mapLinks()

    /**
     * Flatten templates array, with the page name as keys
     *
     * @param array $templates
     *
     * @return string[]
     */
    public function mapTemplates(array $templates): array
    {
        $flattened = [];
        foreach ($templates as $template) {
            if (array_key_exists('exists', $template)) {
                $flattened[$template['*']] = true;
            }
        }//end foreach

        return $flattened;
    }//end mapImages()

    /**
     * Interpolate variables embedded in templates
     *
     * @param string $text
     * @param array  $vars
     *
     * @return null|string
     */
    private function interpolate(string $text, array $vars): ?string
    {
        $regex = [];
        $reps = [];
        foreach ($vars as $var => $val) {
            $regex[] = "/\\{\\{\\s*$var\\s*}}/i";
            $reps[] = $val;
        }//end foreach

        $ret = preg_replace($regex, $reps, $text);

        return $ret ?? $text;
    }//end mapTemplates()

    /**
     * Fetch the page from API
     *
     * @return array
     */
    public function fetch(): ?array
    {
        $content = $this->api->parse($this->optionsToParameters());

        return $content['parse'] ?? null;
    }//end interpolate()
}//end class
