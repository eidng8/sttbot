<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 18:30
 */

namespace eidng8\Wiki\Models;

/**
 * Skill values
 */
class Skills extends Model
{
    /**
     * List of all available skills
     *
     * @var array
     */
    public const SKILLS = ['cmd', 'dip', 'eng', 'med', 'sci', 'sec'];

    /**
     * Skill names
     */
    public const SKILL_NAMES
        = [
            'command'     => 'cmd',
            'diplomacy'   => 'dip',
            'engineering' => 'eng',
            'medicine'    => 'med',
            'science'     => 'sci',
            'security'    => 'sec',
        ];

    /**
     * Command skill value
     *
     * @var int[]
     */
    public $cmd;

    /**
     * Diplomacy skill value
     *
     * @var int[]
     */
    public $dip;

    /**
     * Engineering skill value
     *
     * @var int[]
     */
    public $eng;

    /**
     * Medicine skill value
     *
     * @var int[]
     */
    public $med;

    /**
     * Science skill value
     *
     * @var int[]
     */
    public $sci;

    /**
     * Security skill value
     *
     * @var int[]
     */
    public $sec;

    /**
     * Skills constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this[$key] = $value;
            }//end foreach
        }
    }//end __construct()

    /**
     * Check if the provided name is actually a skill
     *
     * @param string $name
     *
     * @return bool
     */
    public static function isSkill(string $name): bool
    {
        return in_array(strtolower($name), static::SKILLS);
    }//end isSkill()

    /**
     * Return the skill name array
     *
     * @param string[] $skills
     *
     * @return null|string[]
     */
    public static function skillNames(array $skills): ?array
    {
        return array_map(
            function ($skill) {
                return static::skillName($skill);
            },
            $skills
        );
    }//end skillName()

    /**
     * Return the skill name
     *
     * @param string $skill
     *
     * @return null|string
     */
    public static function skillName(string $skill): ?string
    {
        $skill = strtolower($skill);
        if (3 != strlen($skill)) {
            return static::SKILL_NAMES[$skill] ?? null;
        }

        return in_array($skill, static::SKILLS) ? $skill : null;
    }//end skillName()

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $return = [];
        foreach (static::SKILLS as $name) {
            $return[$name] = $this->$name;
        }//end foreach
        return array_filter($return);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $skill = static::skillName($offset);
        if ($skill) {
            parent::offsetSet($skill, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        $skill = static::skillName($offset);
        if ($skill) {
            return parent::offsetExists($skill);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $skill = static::skillName($offset);
        if ($skill) {
            return parent::offsetGet($skill);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $skill = static::skillName($offset);
        if ($skill) {
            parent::offsetUnset($skill);
        }
    }
}//end class
