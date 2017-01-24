<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-22
 * Time: 19:36
 */

namespace eidng8\Wiki\Api;

/**
 * A thin wrapper to MediaWiki API, with file cache facility
 */
class Api
{
    /**
     * Cache root directory
     *
     * @var string
     */
    protected $cacheRoot;

    /**
     * The HTTP instance
     *
     * @var Http
     */
    protected $wiki;

    /**
     * The `parse` action
     *
     * @var Parse
     */
    protected $parse;

    /**
     * The `query` action
     *
     * @var Query
     */
    protected $query;

    /**
     * The `expandtemplates` action
     *
     * @var ExpandTemplates
     */
    protected $expandTemplates;

    /**
     * Api constructor.
     *
     * @param Http   $http      Http instance
     * @param string $cacheRoot
     */
    public function __construct(Http $http, string $cacheRoot)
    {
        $this->wiki = $http;
        $this->cacheRoot = $cacheRoot;
    }//end __construct()

    /**
     * Creates the `parse` Wiki API endpoint
     *
     * @param bool $new `true` to create a new {@link \eidng8\Wiki\Api\Parse}
     *                  instance
     *
     * @return Parse
     */
    public function parse(bool $new = false): Parse
    {
        if ($new || !$this->parse) {
            $this->parse = new Parse($this->wiki);
        }

        $this->parse->cacheRoot($this->cacheRoot);

        return $this->parse;
    }//end parse()

    /**
     * Creates the `query` Wiki API endpoint
     *
     * @param bool $new `true` to create a new {@link \eidng8\Wiki\Api\Query}
     *                  instance
     *
     * @return Query
     */
    public function query(bool $new = false): Query
    {
        if ($new || !$this->query) {
            $this->query = new Query($this->wiki);
        }

        $this->query->cacheRoot($this->cacheRoot);

        return $this->query;
    }//end parse()

    /**
     * Creates the `expandtemplates` Wiki API endpoint
     *
     * @param bool $new `true` to create a new {@link \eidng8\Wiki\Api\Query}
     *                  instance
     *
     * @return ExpandTemplates
     */
    public function expandTemplates(bool $new = false): ExpandTemplates
    {
        if ($new || !$this->expandTemplates) {
            $this->expandTemplates = new ExpandTemplates($this->wiki);
        }

        $this->expandTemplates->cacheRoot($this->cacheRoot);

        return $this->expandTemplates;
    }//end expandTemplates()
}//end class
