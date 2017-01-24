<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 18:54
 */

namespace eidng8\Contracts;

/**
 * Resources that can be referred to by hyperlink
 */
interface Hyperlink
{
    /**
     * URI of the resource, related to site root, without leading slash
     *
     * @return string
     */
    public function uri(): string;

    /**
     * Page name of the resource
     *
     * @return string
     */
    public function page(): string;
}//end interface
