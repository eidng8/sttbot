<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-22
 * Time: 19:49
 */

namespace eidng8\Wiki\Api;

use eidng8\Log\Log;
use eidng8\Traits\Wiki\Cache;
use eidng8\Traits\Wiki\Properties;

/**
 * The WikiMedia `query` action
 */
class Query
{
    use Properties, Cache;

    /**
     * `continue` options
     *
     * @var string
     */
    public static $IIPROP = 'iiprop';

    /**
     * `iiurlwidth` options
     *
     * @var string
     */
    public static $IIURLWIDTH = 'iiurlwidth';

    /**
     * `iiurlheight` options
     *
     * @var string
     */
    public static $IIURLHEIGHT = 'iiurlheight';

    /**
     * `titles` options
     *
     * @var string
     */
    public static $TITLES = 'titles';

    /**
     * `continue` options
     *
     * @var string
     */
    public static $CONTINUE = 'continue';

    /**
     * Thumbnail size
     *
     * @var int
     */
    public static $WIDTH_THUMBNAIL_1X = 100;

    /**
     * Thumbnail 1.5 size
     *
     * @var int
     */
    public static $WIDTH_THUMBNAIL_1X5 = 150;

    /**
     * Thumbnail 2x size
     *
     * @var int
     */
    public static $WIDTH_THUMBNAIL_2X = 200;

    /**
     * Portrait size
     *
     * @var int
     */
    public static $WIDTH_PORTRAIT_1X = 200;

    /**
     * Portrait 1.5 size
     *
     * @var int
     */
    public static $WIDTH_PORTRAIT_1X5 = 300;

    /**
     * Portrait 2x size
     *
     * @var int
     */
    public static $WIDTH_PORTRAIT_2X = 400;

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
        $this->options = [
            static::$PROP       => ['imageinfo'],
            static::$IIPROP     => 'url',
            static::$IIURLWIDTH => '100',
            static::$CONTINUE   => '',
        ];
    }//end resetOptions()

    /**
     * Titles to query
     *
     * @param array $titles
     */
    public function titles(array $titles)
    {
        // if (empty($titles) || !is_array($titles)) { we use type declarations
        if (empty($titles)) {
            return;
        }

        $this->option(static::$TITLES, $titles);
    }//end titles()

    /**
     * Get image information
     *
     * @param array     $files
     * @param int|int[] $width
     *
     * @return array
     */
    public function imageInfo(array $files, $width = 0): array
    {
        if (empty($files)) {
            return [];
        }

        $wantArray = is_array($width); // do we want an array of image sizes?
        $sizes = (array)$width;
        $returns = [];
        foreach ($sizes as $size) {
            $this->properties(['imageinfo']);
            $this->option(static::$TITLES, array_keys($files));
            $size = (int)$size;
            if (!$size) {
                $this->removeOption(static::$IIURLWIDTH);
            } else {
                $this->option(static::$IIURLWIDTH, $size);
            }
            $this->content = null;
            $nails = $this->get();
            if (empty($nails['query']['pages'])) {
                continue;
            }

            foreach ($nails['query']['pages'] as $nail) {
                if (array_key_exists('missing', $nail)) {
                    Log::warn("$nail[title] is missing");
                    continue;
                }

                $key = str_replace('_', ' ', $nail['title']);
                if (!array_key_exists($key, $files)) {
                    $key = str_replace(' ', '_', $nail['title']);
                }
                if (!array_key_exists($key, $files)) {
                    Log::debug("extraeous data returned for $key");
                    continue;
                }

                if (empty($nail['imageinfo'][0]['thumburl'])) {
                    $url = $nail['imageinfo'][0]['url'];
                } else {
                    $url = $nail['imageinfo'][0]['thumburl'];
                }
                $url = Api::removePrefix($url);
                if ($wantArray) {
                    $returns[$files[$key]][] = $url;
                } else {
                    $returns[$files[$key]] = $url;
                }
            }//end foreach
        }//end foreach

        return $returns;
    }//end imageInfo()

    /**
     * Get image info
     *
     * @param string[]  $titles images to get
     * @param int|int[] $width
     *
     * @return array|\string[]
     */
    public function thumbnails(array $titles, $width = 0): array
    {
        // if (empty($titles) || !is_array($titles)) { we use type declaration
        if (empty($titles)) {
            return [];
        }

        $thumbs = [];
        // this crew have their thumbnail with a capped 'h'
        $capped = [
            'Apollo',
            'Augment Riker',
            'Dr. Hippocrates Noah',
            'Emotion Chip Data',
            'Garak, Elim Garak',
            'Goddess of Empathy Troi',
            'Niners Kira Nerys',
            'Niners Rom',
            'Umpire Odo',

        ];
        foreach ($titles as $title) {
            if (in_array($title, $capped)) {
                // please note the CAPPED H
                $thumbs["File:$title Head.png"] = $title;
            } else {
                $thumbs["File:$title head.png"] = $title;
            }
        }//end foreach

        return $this->imageInfo($thumbs, $width);
    }//end thumbnails()

    /**
     * Send the request
     *
     * @param bool $fetch True to fetch new content.
     *
     * @return array|bool content returned from API, or `false` if error
     *                    occurred
     */
    public function get(bool $fetch = false)
    {
        if (!$fetch && $this->content) {
            return $this->content;
        }

        $file = 'query/' . md5(serialize($this->optionsToParameters()));
        $this->content = $this->cache($file, [$this, 'fetch']);

        if (empty($this->content)) {
            return $this->content = null;
        }

        return $this->content;
    }//end get()

    /**
     * Calls the API endpoint
     *
     * @return mixed
     */
    public function fetch()
    {
        return $this->api->query($this->optionsToParameters());
    }//end fetch()
}//end class
