<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-22
 * Time: 21:40
 */

namespace eidng8\Traits\Wiki;

/**
 * Wiki API request options
 */
trait Request
{
    /**
     * Request options
     *
     * @var array
     */
    protected $options;

    /**
     * Retrieves or set the options to be used in request
     *
     * @param array $options omit to retrieve options, or options to use
     * @param bool  $merge   pass `true` to merge `$options` to existing options
     *
     * @return array if `$options` is omitted, returns current options;
     *               otherwise set or merge the provided options
     */
    public function options(array $options = null, bool $merge = false): array
    {
        if (null === $options) {
            return $this->options;
        }

        // We don't need this any more, thanks to type declarations
        // if (!is_array($options)) {
        //     $options = [$options];
        // }

        $old = $this->options;
        if (!$merge || empty($this->options)) {
            $this->options = $options;
        } else {
            $this->options = array_merge($this->options, $options);
        }

        return $old;
    }//end options()

    /**
     * Clear all options
     */
    public function clearOptions()
    {
        $this->options = [];
    }//end clear()

    /**
     * Get or set the specified option
     *
     * @param string $option the option to be get or set
     * @param mixed  $value  omit to get the option's value, or set to new value
     *
     * @return mixed
     */
    public function option(string $option, $value = null)
    {
        if (null === $value) {
            return $this->hasOption($option) ? $this->options[$option] : null;
        }

        $old = null;
        if ($this->hasOption($option)) {
            $old = $this->options[$option];
        }

        $this->options[$option] = $value;

        return $old;
    }//end option()

    /**
     * Checks if the specified option is used
     *
     * @param string $option option to be checked
     *
     * @return bool
     */
    public function hasOption(string $option): bool
    {
        return $this->options && array_key_exists($option, $this->options);
    }//end has()

    /**
     * Remove the specified options
     *
     * @param string $option
     */
    public function removeOption(string $option)
    {
        if ($this->hasOption($option)) {
            unset($this->options[$option]);
        }
    }//end removeOption()

    /**
     * Convert options to an array suitable for API request
     *
     * @return array
     */
    public function optionsToParameters(): array
    {
        $options = [];
        foreach ($this->options as $key => $option) {
            if (is_array($option)) {
                $options[$key] = implode('|', $option);
            } else {
                $options[$key] = $option;
            }
        }//end foreach

        return $options;
    }//end parameters()
}//end trait
