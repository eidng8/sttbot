<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 17:14
 */

namespace eidng8\Contracts;

/**
 * Wiki text parser interface.
 * These parsers don't do any transformation (e.g. to HTML). They merely extract
 * portions of the input text. That is, returns are also wiki texts.
 */
interface WikiTextParser
{
    /**
     * Parse given wiki texts
     *
     * @return mixed
     */
    public function parse();
}//end interface
