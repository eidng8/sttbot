<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-12-03
 * Time: 08:28
 */

namespace eidng8\Wiki\Templates;

use eidng8\Exceptions\EmptyTemplateException;
use eidng8\Wiki\Models\Mission as MissionModel;
use eidng8\Wiki\Models\MissionCost as MissionCostModel;
use eidng8\Wiki\Template;

/**
 * Mission cost template parser
 *
 * @method void set(string $level, int $cost)
 * @method int|void normal(int $cost = null)
 * @method int|void elite(int $cost = null)
 * @method int|void epic(int $cost = null)
 * @method int|void ticket(int $cost = null)
 * @method bool|void useChroniton(int $use = null)
 * @method bool|void useTicket(int $use = null)
 */
class MissionCost extends Template
{
    public const TICKET = 'ticket';

    /**
     * @var MissionCostModel
     */
    protected $model;

    /* @noinspection PhpMissingParentConstructorInspection */
    /**
     * MissionCost constructor.
     *
     * @param string $wikiText
     */
    public function __construct($wikiText)
    {
        $this->found = $wikiText;
        $this->parse();
    }

    /**
     * Parse mission cost string
     *
     * @throws EmptyTemplateException
     */
    public function parse()
    {
        preg_match('/\{\{currency\|([^}]+)}}\s*(.+)/iu', $this->found, $found);
        if (!$found || !$found[0]) {
            throw new EmptyTemplateException();
        }

        $this->model = new MissionCostModel();
        $this->model->useChroniton('crn' === strtolower($found[1]));
        $this->model->useTicket('tik' === strtolower($found[1]));

        if ($this->model->useChroniton()) {
            $parts = explode('/', $found[2]);
            if (count($parts) >= 3) {
                $this->model->normal(intval($parts[MissionModel::NORMAL]));
                $this->model->elite(intval($parts[MissionModel::ELITE]));
                $this->model->epic(intval($parts[MissionModel::EPIC]));
            } else {
                $triple = Triple::load($found[2]);
                if (null === $triple) {
                    $this->model->normal(0);
                    $this->model->elite(0);
                    $this->model->epic(0);
                } else {
                    $this->model->normal($triple->normal());
                    $this->model->elite($triple->elite());
                    $this->model->epic($triple->epic());
                }
            }
        }

        if ($this->model->useTicket()) {
            $this->model->ticket(intval($found[2]));
        }
    }//end parse()

    /**
     * @return MissionCostModel
     */
    public function get(): MissionCostModel
    {
        return $this->model;
    }//end get()

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->model, $name], $arguments);
    }//end __call()
}//end class
