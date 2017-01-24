<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-17
 * Time: 23:54
 */

namespace eidng8\Wiki;

use eidng8\Wiki\Api\ExpandTemplates;
use eidng8\Wiki\Api\Parse;
use eidng8\Wiki\Api\Query;

/**
 * Wiki process base class
 */
abstract class WikiBase
{
    /**
     * Parse API instance
     *
     * @var Parse
     */
    protected $parse;

    /**
     * Query API instance
     *
     * @var Query
     */
    protected $query;

    /**
     * ExpandTemplates API instance
     *
     * @var ExpandTemplates
     */
    protected $expandTemplates;

    /**
     * WikiBase constructor.
     *
     * @param Parse           $parse
     * @param Query           $query
     * @param ExpandTemplates $expandTemplates
     */
    public function __construct(
        Parse $parse,
        Query $query,
        ExpandTemplates $expandTemplates
    ) {
        $this->parse = $parse;
        $this->query = $query;
        $this->expandTemplates = $expandTemplates;
    }//end __construct()
}//end class
