<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 17:33
 */

namespace eidng8\Wiki\Templates;

use eidng8\Wiki\Api\Parse;
use eidng8\Wiki\Api\Query;
use eidng8\Wiki\Models\CrewMember;
use eidng8\Wiki\Models\Skills;
use eidng8\Wiki\Template;

/**
 * Crew all wiki text template parser
 */
class CrewList extends Template
{
    /**
     * Maximum skills statistics
     *
     * @var array
     */
    public $maxSkills = [];

    /**
     * All parsed crew members
     *
     * @var CrewMember[]
     */
    protected $crew;

    /**
     * @var Parse
     */
    protected $parse;

    /**
     * @var Query
     */
    protected $query;

    /**
     * CrewList constructor.
     *
     * @param string $wikiText wiki text to be parsed
     * @param Parse  $parse
     * @param Query  $query    pass a {@see Query} instance to also retrieve
     *                         picture URL
     */
    public function __construct(
        string $wikiText,
        Parse $parse,
        Query $query = null
    ) {
        $this->parse = $parse;
        $this->query = $query;
        parent::__construct($wikiText, 'MPCrewList');
    }//end __construct()

    /**
     * Total number of available members
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->crew);
    }//end count()

    /**
     * Retrieves all crew members, in indexed (not associated) array
     *
     * @return CrewMember[]
     */
    public function get(): array
    {
        return $this->crew;
    }//end parseCrew()

    /**
     * {@inheritdoc}
     */
    public function parse(): array
    {
        parent::parse();

        return $this->crew = $this->parseCrew();
    }//end parseMember()

    /**
     * Parse all crew member from the given wiki text
     *
     * @return CrewMember[] Array of parsed crew members
     */
    public function parseCrew(): array
    {
        $members = [];
        foreach ($this->found as $member) {
            $member = $this->parseMember($member);
            $members[$member->name] = $member;
        }//end foreach

        $members = $this->fetchPictures($members);
        foreach ($members as $member) {
            $this->fetchDetail($member);
            $this->stats($member);
        }//end foreach

        return $members;
    }//end fetchPictures()

    /**
     * Parse one crew member
     *
     * @param $wikiText
     *
     * @return CrewMember
     */
    public function parseMember(string $wikiText): CrewMember
    {
        $lines = explode("\n", $wikiText);
        array_shift($lines);
        $member = new CrewMember();

        $member->traits = array_values(
            array_map(
                'mb_strtolower',
                array_filter(
                    array_map('trim', static::explode(array_pop($lines)))
                )
            )
        );

        foreach ($lines as $line) {
            $aaa = array_map(
                'trim',
                explode('=', substr($line, 2))
            );
            list($prop, $val) = $aaa;
            $member[$prop] = $val;
        }//end foreach

        return $member;
    }//end fetchDetail()

    /**
     * Get all crew member picture url
     *
     * @param CrewMember[] $crew
     *
     * @return CrewMember[]
     */
    public function fetchPictures(array $crew): array
    {
        if (empty($this->query)) {
            return $crew;
        }

        $chunks = array_chunk($crew, 50);
        foreach ($chunks as $chunk) {
            $images = $this->query->thumbnails(array_column($chunk, 'name'));
            foreach ($chunk as $member) {
                $member['picture'] = $images[$member->name] ?? null;
            }//end foreach
        }//end foreach

        return $crew;
    }//end skillList()

    /**
     * Fetch crew member detail information
     *
     * @param CrewMember $member
     */
    public function fetchDetail(CrewMember $member): void
    {
        $this->parse->resetOptions();
        // Tuvix has an extra section on how to get it.
        $this->parse->page($member->name, 'Tuvix' == $member->name ? 3 : 2);
        $this->parse->get(true);
        $this->parse->tables();
        $table = $this->parse->table();
        $skills = $this->skillList($table);
        list($max, $raw) = $this->skillValue($table, $skills);
        $tmp = [];
        $tmpr = [];
        foreach ($skills as $idx => $skill) {
            $tmp[$skill] = $max[$idx];
            $tmpr[$skill] = $raw[$idx];
        }//end foreach
        $member->skills = new Skills($tmp);
        $member->rawSkills = new Skills($tmpr);
    }//end skillValue()

    /**
     * Parse table header and extract skill list
     *
     * @param string $table
     *
     * @return array|null
     */
    public function skillList(string $table): ?array
    {
        $regex = '/^\s*!\s*lvl\s*$(.+?)\|-.*?$/imsu';
        if (!preg_match($regex, $table, $found)) {
            return null;
        }

        $regex = '/\{\{skill\|(.+?)}}/imsu';
        if (!preg_match_all($regex, explode("\n", $found[1])[1], $found)) {
            return null;
        }

        return array_map('trim', array_map('strtolower', $found[1]));
    }//end all()

    /**
     * Parse table and extract skill value
     *
     * @param string $table
     * @param array  $skills
     *
     * @return array|null
     */
    public function skillValue(string $table, array $skills): ?array
    {
        $regex = '/^\s*\|\s*\d+\s*$(.+?)\|[}-].*?$/imsu';
        if (!preg_match_all($regex, $table, $levels)) {
            return null;
        }

        $raw = [];
        $max = array_fill(0, count($skills), array_fill(0, 2, 0));
        foreach ($levels[1] as $level) {
            $lines = array_filter(explode("\n", $level));
            $regex = '/\{\{sp\|(.+?)}}/imsu';
            foreach ($lines as $line) {
                if (!preg_match_all($regex, $line, $values)) {
                    continue;
                }
                foreach ($values[1] as $idx => $value) {
                    $value = array_map(
                        function ($val) {
                            return intval($val);
                        },
                        explode('|', trim($value))
                    );
                    $raw[$idx] = $value;
                    $low = $value[0] + $value[1];
                    $high = $value[0] + $value[2];
                    if ($low > $high) {
                        $tmp = $low;
                        $low = $high;
                        $high = $tmp;
                    }
                    $max[$idx][0] = max($max[$idx][0], $low);
                    $max[$idx][1] = max($max[$idx][1], $high);
                }//end foreach
            }//end foreach
        }//end foreach

        return [$max, $raw];
    }//end get()

    /**
     * Calculates various statistics
     *
     * Currently there's only the max skill value here. This is the pivot point
     * where more statistics were added later. This will be the entry point of
     * all those methods by then.
     *
     * @param CrewMember $member
     */
    protected function stats(CrewMember $member)
    {
        foreach ($member->skills as $skill => $val) {
            if (empty($val)) {
                continue;
            }

            $val = max($val);
            if (empty($this->maxSkills[$skill][$member->stars][1])
                || $this->maxSkills[$skill][$member->stars][1] < $val
            ) {
                $this->maxSkills[$skill][$member->stars] = [$member, $val];
            }
        }//end foreach
    }//end byTraits()

    /**
     * Retrieve a crew member by name
     *
     * @param string $name name of the member to retrieve
     *
     * @return CrewMember
     */
    public function byName($name): ?CrewMember
    {
        return empty($this->crew[$name]) ? null : $this->crew[$name];
    }//end parse()

    /**
     * Find all crew that possess given traits
     *
     * @param array $traits
     *
     * @return array
     */
    public function byTraits(array $traits): ?array
    {
        $crew = [];
        foreach ($this->crew as $member) {
            if ($member->hasTraits($traits)) {
                $crew[] = $member;
            }
        }//end foreach
        return $crew;
    }//end each()

    /**
     * Export all crew members as array
     *
     * @return array
     */
    public function export(): array
    {
        $crew = [];
        $this->each(
            function (CrewMember $member) use (&$crew) {
                $member = $member->toArray();
                unset($member['charpage'], $member['page']);
                $crew[] = $member;
            }
        );

        return $crew;
    }//end regex()

    /**
     * Iterate through all crew members and call the given function.
     *
     * @param callable $func
     */
    public function each(callable $func): void
    {
        foreach ($this->crew as $name => $member) {
            call_user_func_array($func, [$member, $name]);
        }//end foreach
    }//end export()

    /**
     * @param string $skill
     *
     * @return array
     */
    public function allMax(string $skill): array
    {
        return $this->maxSkills[$skill];
    }//end max()

    /**
     * @param string $skill
     * @param int    $stars
     *
     * @return array
     */
    public function max(string $skill, int $stars = 5): array
    {
        return $this->maxSkills[$skill][max(1, min($stars, 5))];
    }//end max()

    /**
     * {@inheritdoc}
     */
    protected function regex(): string
    {
        return "/^\\s*\\{\\{{$this->name}.+?}}\\s*$/imsu";
    }//end stats()
}//end class
