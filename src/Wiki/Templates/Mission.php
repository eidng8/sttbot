<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-20
 * Time: 11:13
 */

namespace eidng8\Wiki\Templates;

use eidng8\Exceptions\EmptyTemplateException;
use eidng8\Exceptions\InvalidTemplateException;
use eidng8\Log\Log;
use eidng8\Wiki\Models\Mission as MissionModel;
use eidng8\Wiki\Models\MissionStep;
use eidng8\Wiki\Models\ReqAndBonus;
use eidng8\Wiki\Template;

/**
 * Parse a single mission page
 *
 * @method MissionModel get()
 */
class Mission extends Template
{
    private $advanced = false;

    /**
     * Mission constructor.
     * The `advanced` key in the `$options` array denotes if it's adv cadet
     *
     * @param string $wikiText
     * @param string $name
     * @param array  $options
     */
    public function __construct(
        $wikiText,
        $name = null,
        $options = null
    ) {
        $this->advanced = !empty($options) && !empty($options['advanced']);
        parent::__construct($wikiText, $name, $options);
    }//end __construct()

    /**
     * {@inheritdoc}
     */
    public function parse(): MissionModel
    {
        $this->found = [];
        $sections = $this->sections(2, $this->wikiText);
        $info = $this->parseInfoBox($sections[0]);
        if ('away team' == $info->type()) {
            $this->found = array_merge(
                $this->found,
                $this->parseWalkThru($sections[1])
            );
        }
        $this->found['info'] = $info;

        return $this->found = $this->createModel();
    }//end parse()

    /**
     * Process info box
     *
     * @param string $mission
     *
     * @return InfoBox
     */
    public function parseInfoBox(string $mission): InfoBox
    {
        $info = new InfoBox($mission);
        $this->name = $info->name();

        return $info;
    }//end parseInfoBox()

    /**
     * Process mission walk through
     *
     * @param string $mission
     *
     * @return string[]
     */
    public function parseWalkThru(string $mission): array
    {
        $regex = '/^\{\{MWHead.+?^}}$/imsu';
        preg_match_all($regex, $mission, $intro);
        $intro = $this->parseMWHead($intro[0][0]);
        $steps = $this->parseMWalk($mission);

        return compact('intro', 'steps');
    }//end parseWalkThru()

    /**
     * Process mission walk through header
     *
     * @param string $head
     *
     * @return string[]
     */
    public function parseMWHead(string $head): array
    {
        $lines = explode("\n", $head);

        $intro = [];
        foreach ($lines as $line) {
            if (preg_match_all(
                '/^\s*\|\s*ChronCost\s*=\s*(\{\{triple[^\}]+}})$/iu',
                $line,
                $found
            )) {
                // We've got cost fro info box
                // $intro['cost'] = new MissionCost($found[1][0]);
            } elseif (preg_match_all(
                '/^\s*<!--Traits-->\s*((?:\|[^|]+)+)$/iu',
                $line,
                $found
            )) {
                $traits = Template::explode($found[1][0]);
                if (!empty($traits)) {
                    $intro['traits'] = array_values(
                        array_map(
                            'strtolower',
                            array_filter($traits)
                        )
                    );
                }
            }
        }//end foreach

        return $intro;
    }//end parseMWHead()

    /**
     * Process mission steps
     *
     * @param string $text
     *
     * @return string[]
     */
    public function parseMWalk(string $text): array
    {
        $regex = '/^\{\{MWalk(\d)\s*$.+?^}}$/imsu';
        preg_match_all($regex, $text, $found);
        $steps = [];
        foreach ($found[0] as $step) {
            $steps[] = $this->parseStep($step);
        }//end foreach

        return $steps;
    }//end parseMWalk()

    /**
     * Process a mission step
     *
     * @param string $text
     *
     * @return string[]
     */
    public function parseStep(string $text): array
    {
        $columns = [
            'steptitle'  => 'name',
            'choice'     => 'alt',
            'bonustrait' => 'traits',
            'otherreq'   => 'locks',
            'skillreq'   => 'skills',
        ];
        $fields = implode('|', array_keys($columns));
        $regex
            = "/^\\h*\\|\\h*($fields)([a-z])?(Pic)?\\h*=\\h*(.+)?\\h*$/imu";
        preg_match_all($regex, $text, $found);

        $props = [];
        // $found[1] holds the list of keys, e.g. 'SkillReq', 'Choice', etc.
        $found[1] = array_map('strtolower', $found[1]);
        // $found[2] holds the step alternative, e.g. 'A', 'B', etc.
        $found[2] = array_map('strtolower', $found[2]);
        // $found[3] is only useful for alternative thumbnail
        $found[3] = array_map('strtolower', $found[3]);
        // $found[4] holds value of properties
        // loop through all found properties
        foreach ($found[4] as $idx => $value) {
            $key = $columns[$found[1][$idx]];
            if (!empty($found[2][$idx])) {
                $alt = ord($found[2][$idx]) - ord('a');
            } else {
                $alt = null;
            }
            if ('choice' == $found[1][$idx] && 'pic' == $found[3][$idx]) {
                $props['images'][$alt] = trim($value);
            } elseif (null !== $alt) {
                $props[$key][$alt] = trim($value);
            } else {
                $props[$key] = trim($value);
            }

            if (in_array($key, ['locks', 'skills', 'traits'])) {
                try {
                    $values = [];
                    if (!empty($value)) {
                        preg_match(
                            '/(\{\{[^}]+}})(?:.*Adv:\h*(\{\{[^}]+}}))?/iu',
                            $value,
                            $values
                        );
                    }
                    $props[$key][$alt] = $this->parseStepProp(
                        $key,
                        empty($values[1]) ? '' : $values[1],
                        empty($values[2]) ? '' : $values[2]
                    );
                } catch (InvalidTemplateException | EmptyTemplateException $e) {
                    Log::notice($e->getMessage());
                } catch (\Exception $e) {
                    Log::warn($e->getMessage());
                }
            }
        }//end foreach

        return $props;
    }//end parseStep()

    /**
     * Parse mission step properties
     *
     * @param string $key
     * @param string $values
     * @param string $advValues
     *
     * @return array|Triple|mixed|null
     */
    public function parseStepProp(
        string $key,
        string $values,
        string $advValues
    ) {
        if (empty($values)) {
            return null;
        }

        $loadTriple = function ($value) {
            if (empty($value) || '{' !== $value[0]) {
                throw new EmptyTemplateException(
                    "$this->name invalid triple: $value"
                );
            }

            $triple = Triple::load($value);
            if (!$triple) {
                throw new InvalidTemplateException(
                    "$this->name invalid triple: $value"
                );
            }

            return $triple;
        };

        $return = null;
        switch ($key) {
            case 'skills':
                $triple = $loadTriple($values);
                if ($this->advanced) {
                    try {
                        $adv = $loadTriple($advValues);
                        $adv->name = $triple->name;
                    } catch (\Exception $ex) {
                        $adv = Triple::empty($triple->name);
                    }
                    $triple = $adv;
                }
                $return = $triple;
                break;

            case 'traits':
                $triple = $loadTriple(trim($values));
                if ($this->advanced) {
                    try {
                        $adv = $loadTriple(trim($advValues));
                        $adv->name = $triple->name;
                    } catch (\Exception $e) {
                        $adv = Triple::empty($triple->name);
                    }
                    $triple = $adv;
                }

                // fill empty values with guess values
                $guessFactor = 2;
                if (!$triple->elite()) {
                    $triple->elite($triple->normal() * $guessFactor);
                }
                if (!$triple->epic()) {
                    $triple->epic($triple->elite() * $guessFactor);
                }
                $return = $triple;
                break;

            case 'locks':
                $locks = explode('|', trim($values, " {}-\t\0\xb"));
                array_shift($locks);
                $return = array_filter(
                    array_map(
                        'strtolower',
                        array_map('trim', $locks)
                    )
                );
                break;
        }

        return $return;
    }//end parseStepProp()

    /**
     * Create a mission model
     *
     * @return MissionModel
     */
    public function createModel(): MissionModel
    {
        $model = new MissionModel();
        /* @var InfoBox $info */
        $info = $this->found['info'];
        $model->name = $info->name();
        $model->page = $info->name();
        $model->episode = $info->episode();
        $model->index = $info->sequence();
        $model->cost = $info->cost()->get();
        $model->image = $info->image();
        if ('away team' != $info->type()) {
            $model->type = MissionModel::SPACE_BATTLE;
        } else {
            $model->type = MissionModel::AWAY_TEAM;
            $model->traits = $this->found['intro']['traits'];

            $model->steps = [];
            foreach ($this->found['steps'] as $step) {
                $values = new MissionStep();
                $values->name = $step['name'];
                $values->alt = $step['alt'];
                $values->images = $step['images'];

                $values->skills = array_map(
                    [$this, 'loadReqAndBonusModel'],
                    $step['skills']
                );

                if (!empty($step['traits'])) {
                    $values->traits = array_map(
                        [$this, 'loadReqAndBonusModel'],
                        $step['traits']
                    );
                }

                if (!empty($step['locks'])) {
                    $values->locks = $step['locks'];
                }

                $model->steps[] = $values;
            }//end foreach
        }

        $this->validateModel($model);

        return $model;
    }//end createModel()

    /**
     * @param MissionModel $model
     *
     * @return bool
     */
    public function validateModel(MissionModel $model): bool
    {
        return $model->validate();
    }//end loadReqAndBonusModel()

    /**
     * @param Triple|null $values
     *
     * @return ReqAndBonus|null
     */
    public function loadReqAndBonusModel(Triple $values = null)
    {
        if (empty($values)) {
            return null;
        }

        $model = new ReqAndBonus();
        $model->name($values->name());
        $model->set($values->get());

        return $model;
    }
}//end class
