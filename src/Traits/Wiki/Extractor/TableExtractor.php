<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 15:14
 */

namespace eidng8\Traits\Wiki\Extractor;

/**
 * Extract entire table from wiki text
 */
trait TableExtractor
{
    /**
     * Wiki text to be processed
     *
     * @var string
     */
    protected $wikiText;

    /**
     * Tables found from the wiki text
     *
     * @var string[]
     */
    protected $foundTables = [];

    /**
     * Number of tables extracted
     *
     * @return int
     */
    public function numTables(): int
    {
        return $this->tablesExtracted() ? count($this->foundTables) : 0;
    }//end tables()

    /**
     * Check if there is any extracted table
     *
     * @return bool
     */
    public function tablesExtracted(): bool
    {
        if (false === $this->foundTables) {
            return false;
        }

        if (empty($this->foundTables)) {
            $this->tables();
        }

        return true;
    }//end numTables()

    /**
     * Extracts all tables
     *
     * @param string $text Wiki text to be parsed
     */
    public function tables(string $text = null)
    {
        if (empty($text)) {
            $text = $this->wikiText;
        }

        /* @noinspection SpellCheckingInspection */
        $regex = '/(^\s*\{\|.+?^\s*\|}\s*)/imsu';
        preg_match_all($regex, $text, $this->foundTables);
        if (false !== $this->foundTables) {
            $this->foundTables = $this->foundTables[1];
        }
    }//end table()

    /**
     * Retrieves raw wiki text of the table specified by index
     *
     * @param int $idx Index of the table to retreive
     *
     * @return string
     */
    public function table(int $idx = 0): string
    {
        return $this->tablesExtracted() ? $this->foundTables[$idx] : null;
    }//end tablesExtracted()
}//end trait
