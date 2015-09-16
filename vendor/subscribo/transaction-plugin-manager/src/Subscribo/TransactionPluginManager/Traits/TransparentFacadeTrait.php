<?php

namespace Subscribo\TransactionPluginManager\Traits;

use BadMethodCallException;

/**
 * Trait TransparentAdapterTrait
 *
 * expects these properties to be defined on class using this trait:
 *
 * protected $instanceOfObjectBehindFacade;
 *
 * protected static $classNameOfObjectBehindFacade; //if static calls should be supported as well
 *
 * @package Subscribo\TransactionPluginManager
 */
trait TransparentFacadeTrait
{
    public static function __callStatic($name, array $arguments)
    {
        if (empty(static::$classNameOfObjectBehindFacade)) {
            throw new BadMethodCallException(sprintf("Invalid static method call to '%s'", $name));
        }
        return call_user_func_array([static::$classNameOfObjectBehindFacade, $name], $arguments);
    }

    public function __call($name, array $arguments)
    {
        return call_user_func_array([$this->instanceOfObjectBehindFacade, $name], $arguments);
    }

    public function __set($name, $value)
    {
        $this->instanceOfObjectBehindFacade->$name = $value;
    }

    public function __get($name)
    {
        return $this->instanceOfObjectBehindFacade->$name;
    }

    public function __isset($name)
    {
        return (isset($this->instanceOfObjectBehindFacade->$name));
    }

    public function __unset($name)
    {
        unset($this->instanceOfObjectBehindFacade->$name);
    }
}
