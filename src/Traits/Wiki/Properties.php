<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-22
 * Time: 21:45
 */

namespace eidng8\Traits\Wiki;

/**
 * Wiki API `prop` arguments
 */
trait Properties
{
    use Request;

    /**
     * the `prop` key
     *
     * @var string
     */
    protected static $PROP = 'prop';

    /**
     * retrieves the original wikitext that was parsed
     *
     * @var string
     */
    protected static $PROP_WIKITEXT = 'wikitext';

    /**
     * Retrieves the templates in the parsed wikitext
     *
     * @var string
     */
    protected static $PROP_TEMPLATES = 'templates';

    /**
     * Retrieves the internal links in the parsed wikitext
     *
     * @var string
     */
    protected static $PROP_LINKS = 'links';

    /**
     * Retrieves the images in the parsed wikitext
     *
     * @var string
     */
    protected static $PROP_IMAGES = 'images';

    /**
     * Retrieves or sets properties to request
     *
     * @param array $properties omit to retrieve properties that will be used
     *                          in the request, or the properties to request
     *
     * @return array returns properties that will be used in the request if
     *               `$properties` is omitted; otherwise returns nothing
     */
    public function properties(array $properties = null): array
    {
        if (empty($properties)) {
            return $this->option(static::$PROP);
        }

        $old = $this->option(static::$PROP);
        $this->option(static::$PROP, $properties);

        return $old;
    }//end properties()

    /**
     * Checks if the given property is set
     *
     * @param string $property
     *
     * @return bool
     */
    public function hasProperty(string $property): bool
    {
        $prop = $this->option(static::$PROP);

        return !empty($prop)
            && ($property == $prop
                || is_array($prop)
                && array_search($property, $prop) !== false);
    }//end has()

    /**
     * Add a new property
     *
     * @param string $property property to be added
     */
    public function addProperty(string $property)
    {
        if (empty($this->option(static::$PROP))) {
            $this->option(static::$PROP, []);
        } elseif (!is_array($this->option(static::$PROP))) {
            $this->option(static::$PROP, [$this->option(static::$PROP)]);
        }
        $props = $this->option(static::$PROP);
        $props[] = $property;
        $this->option(static::$PROP, $props);
    }//end addProperty()

    /**
     * Remove the given property
     *
     * @param string $property property to be removed
     */
    public function removeProperty(string $property)
    {
        $props = $this->option(static::$PROP);
        if (empty($props)) {
            return;
        }

        if (!is_array($props)) {
            if ($property === $props) {
                $this->removeOption(static::$PROP);
            }

            return;
        }

        $key = array_search($property, $props);
        if (false !== $key) {
            unset($props[$key]);
            $this->option(static::$PROP, $props);
        }
    }//end removeProperty()
}//end trait
