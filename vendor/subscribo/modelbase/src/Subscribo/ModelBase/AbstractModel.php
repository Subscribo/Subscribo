<?php namespace Subscribo\ModelBase;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class AbstractModel
 *
 * @package Subscribo\ModelBase
 */
abstract class AbstractModel extends Eloquent {

    /**
     * Rules for validation
     *
     * @var array
     */
    public static $rules = array();

    /**
     * Rules for modifications before validation
     *
     * @var array
     */
    public static $modificationRulesBeforeValidation = array();

    /**
     * Rules for modifications after validation
     *
     * @var array
     */
    public static $modificationRulesAfterValidation = array();

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name (string) or an array of names of related models
     *
     * @var array
     */
    protected $availableRelations = array();

    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array();

    /**
     * Property name (usually camel cased) to column (attribute) name (usually snake cased) map
     *
     * @var array
     */
    protected $attributeMap = array();

    /**
     * Properties, which could be used for filtering
     *
     * @return array
     */
    public function getFilterableByProperties()
    {
        return array();
    }

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name
     *
     * @return array
     */
    public function getAvailableRelations()
    {
        return $this->availableRelations;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if (empty($this->attributeMap[$key])) {
            parent::__set($key, $value);
        } else {
            parent::__set($this->attributeMap[$key], $value);
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (empty($this->attributeMap[$key])) {
            return parent::__get($key);
        } else {
            return parent::__get($this->attributeMap[$key]);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        if (empty($this->attributeMap[$key])) {
            return parent::__isset($key);
        } else {
            return parent::__isset($this->attributeMap[$key]);
        }
    }

    /**
     * @param string $key
     */
    public function __unset($key)
    {
        if ( ! empty($this->attributeMap[$key])) {
            parent::__unset($this->attributeMap[$key]);
        }
        parent::__unset($key);
    }

}
