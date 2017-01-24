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
    public const AWAY_TEAM = 'away team';

    public const CADET_CHALLENGE = 'cadet challenge';

    public const COST = 'cost';

    public const CREW = 'crew';

    public const DISTRESS_CALLS = 'distress calls';

    public const EPISODE = 'episode';

    public const MISSION = 'mission';

    public const SPACE_BATTLE = 'space battle';

    public const TITLE = 'title';

    public const TRIPLE = '/{{triple\d*\|\s*(\d+)\s*\|\s*(\d+)\s*\|\s*(\d+)\s*}}/iu';

    public const TYPE = 'type';

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
     * Parse the wiki text and returns all extracted templates
     *
     *@return string[]
     */
    public function parse()
    {
        preg_match_all($this->regex(), $this->wikiText, $this->found);

        return $this->found = $this->found[0];
    }//end extractNames()

    /**
     * Regular expression to use when extracting wiki text template
     *
     * @return string
     */
    protected function regex(): string
    {
        return "/^\\s*\\{\\{{$this->name}.+?^\s*}}$/imsu";
    }//end __construct()

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
    }//end get()

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

        return null;
    }//end parse()

    /**
     * Returns all found template texts
     *
     * @return string[]
     */
    public function get()
    {
        return $this->found;
    }//end sections()

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
    }//end regex()
}//end class
