<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 17:11
 */

namespace eidng8\Wiki;

use eidng8\Contracts\WikiTextParser;
use eidng8\Exceptions\EmptyTemplateException;
use eidng8\Log\Log;

/**
 * Template wiki text extractor
 */
abstract class Template implements WikiTextParser
{

    const AWAY_TEAM       = 'away team';

    const CADET_CHALLENGE = 'cadet challenge';

    const COST            = 'cost';

    const CREW            = 'crew';

    const DISTRESS_CALLS  = 'distress calls';

    const EPISODE         = 'episode';

    const MISSION         = 'mission';

    const SPACE_BATTLE    = 'space battle';

    const TITLE           = 'title';

    const TRIPLE          = '/{{triple\d*\|\s*(\d+)\s*\|\s*(\d+)\s*\|\s*(\d+)\s*}}/iu';

    const TYPE            = 'type';

    /**
     * The wiki text to be parsed
     *
     * @var string
     */
    protected $wikiText;

    /**
     * Name of the template
     *
     * @var string[]
     */
    protected $name;

    /**
     * Array of all extracted templates
     *
     * @var string[]
     */
    protected $found;


    /**
     * Template constructor.
     *
     * @param string $wikiText wiki text to be parsed
     * @param string $name     name of the template to extract
     * @param mixed  $options
     */
    public function __construct(
        string $wikiText,
        string $name = null,
        $options = null
    ) {
        $this->wikiText = $wikiText;
        if (!empty($name)) {
            $this->name = $name;
        }
        $this->parse();
    }//end explode()


    /**
     * Split the given template string into array
     *
     * @param $text
     *
     * @return string[]
     */
    public static function explode($text): array
    {
        $traits = array_filter(
            preg_split(
                '/[|,]/iu',
                trim($text, "()[]{} \t\n\r\0\xb")
            ),
            function ($val) {
                return !preg_match('/<!--.*-->/', $val);
            }
        );
        sort($traits);

        return $traits;
    }//end extractNames()

    /**
     * Get a list of all names of a given level
     *
     * @param int    $level
     * @param string $text
     *
     * @return string[]
     */
    // public static function extractNames(int $level, string $text): array
    // {
    //     $regex = str_repeat('=', $level);
    //     $regex = "/$regex\\[\\[[^\\]]+]]$regex/imsu";
    //     preg_match_all($regex, $text, $found);
    //     return $found;
    // }//end load()

    /**
     * Create intance according to wiki text
     *
     * @param string      $wikiText
     * @param string|null $name
     * @param mixed       $options
     *
     * @return static
     */
    public static function load(
        string $wikiText,
        string $name = null,
        $options = null
    ) {
        try {
            return new static($wikiText, $name, $options);
        } catch (EmptyTemplateException $ex) {
            Log::notice('Empty template', [static::class, $ex, $wikiText]);
        }
        // catch (\Exception $ex) {
        //     Log::warn(
        //         "Failed to parse template: {$ex->getMessage()}",
        //         [static::class, $ex, $wikiText]
        //     );
        //     return null;
        // }

        return null;
    }//end __construct()


    /**
     * Returns all found template texts
     *
     * @return string[]
     */
    public function get()
    {
        return $this->found;
    }//end get()


    /**
     * Parse the wiki text and returns all extracted templates
     *
     * @return string[]
     */
    public function parse()
    {
        preg_match_all($this->regex(), $this->wikiText, $this->found);

        return $this->found = $this->found[0];
    }//end parse()


    /**
     * Get section
     *
     * @param $level
     * @param $text
     *
     * @return array
     */
    public function sections($level, $text)
    {
        $regex = '/^={' . $level . '}[^=]+={' . $level . '}$/imu';
        preg_match_all(
            $regex,
            $text,
            $offsets,
            PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE
        );
        $sections = [];
        $last = 0;
        foreach ($offsets[0] as $offset) {
            $sections[] = substr($text, $last, $offset[1] - $last);
            $last = $offset[1];
        }//end foreach
        $sections[] = substr($text, $last);

        return $sections;
    }//end sections()


    /**
     * Regular expression to use when extracting wiki text template
     *
     * @return string
     */
    protected function regex(): string
    {
        return "/^\\s*\\{\\{{$this->name}.+?^\s*}}$/imsu";
    }//end regex()
}//end class
