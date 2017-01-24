<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-20
 * Time: 10:22
 */

namespace eidng8\Wiki\Templates;

use eidng8\Exceptions\EmptyTemplateException;
use eidng8\Wiki\Template;

/**
 * Triple template parser
 *
 * @see http://startrektimelineswiki.com/wiki/Template:Triple
 */
class Triple extends Template
{
    /**
     * Creates an empty instance with given names
     *
     * @param array|null $names
     *
     * @return Triple
     */
    public static function empty(array $names = null): Triple
    {
        $ns = '';
        if (!empty($names)) {
            $ns = '|' . implode('|', $names);
        }

        return new static("{{triple{$ns}|0|0|0}}");
    }//end empty()

    /**
     * {@inheritdoc}
     */
    public function parse(): array
    {
        $this->name = [];
        $this->found = [];
        $parts = explode('|', trim($this->wikiText, "{ }\t\n\r\0\xb"));
        if (count($parts) < 2) {
            throw new EmptyTemplateException();
        }

        array_shift($parts);
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) {
                $this->found[] = 0;
            } elseif (is_numeric($part)) {
                $this->found[] = intval($part);
            } elseif ('?' == $part) {
                $this->found[] = 0;
            } elseif ('??' == $part) {
                $this->found[] = 0;
                $this->found[] = 0;
            } elseif (!empty($this->found)) {
                break;
            } else {
                $this->name[] = mb_strtolower($part);
            }
        }//end foreach

        // if (empty(array_filter($this->found))) {
        //     throw new EmptyTemplateException();
        // }

        while (count($this->found) < 3) {
            $this->found[] = 0;
        }

        return $this->found;
        /*
        preg_match_all(
            '/{{triple(?:\|\s*([^\d{}?]*?))?\|\s*([\d?]*)(?:(?:\s*\|\s*([\d?]*)\s*(?:\|\s*([\d?]*))?)?\s*\|.*?)?}}/imsu',
            $this->wikiText,
            $this->found
        );

        array_shift($this->found);
        $this->found = array_values(
            array_filter(
                array_map(
                    function ($key, $item) {
                        $str = trim($item[0], " -\t\0\xb");
                        if (empty($str)) {
                            return $key > 1 ? -1 : null;
                        }
                        return is_numeric($str) ? (int)$str : $str;
                    },
                    array_keys($this->found),
                    $this->found
                )
            )
        );

        if (!is_numeric($this->found[0])) {
            $this->name = array_filter(Template::explode($this->found[0]));
            array_shift($this->found);
        }

        $this->found = array_slice($this->found, 0, 3);
        return $this->found;
        */
    }//end parse()

    /**
     * Subjects this triple refer to
     *
     * @return string[]
     */
    public function name(): array
    {
        return $this->name;
    }//end subjects()

    /**
     * Normal difficulty level
     *
     * @param int $value
     *
     * @return int
     */
    public function normal(int $value = null): int
    {
        if (is_numeric($value)) {
            $this->found[0] = $value;
        }

        return $this->found[0];
    }//end normal()

    /**
     * Elite difficulty level
     *
     * @param int $value
     *
     * @return int
     */
    public function elite(int $value = null): int
    {
        if (is_numeric($value)) {
            $this->found[1] = $value;
        }

        return $this->found[1];
    }//end elite()

    /**
     * Epic difficulty level
     *
     * @param int $value
     *
     * @return int
     */
    public function epic(int $value = null): int
    {
        if (is_numeric($value)) {
            $this->found[2] = $value;
        }

        return $this->found[2];
    }//end epic()

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty(array_filter($this->found));
    }//end isEmpty()
}//end class
