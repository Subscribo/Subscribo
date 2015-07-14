<?php namespace Subscribo\Environment;

use \Subscribo\Environment\Environment;

class EnvironmentRegistry {

    protected static $_instances = array();

    /**
     * @param string $key (when not provided, then default environment instance is returned (usual case))
     * @return \Subscribo\Environment\EnvironmentInterface
     */
    public static function getInstance($key = 'default')
    {
        if ( ! isset(static::$_instances[$key])) {
            static::$_instances[$key] = static::make($key);
        }
        return static::$_instances[$key];
    }

    /**
     * @param string $key
     * @return \Subscribo\Environment\EnvironmentInterface
     */
    protected static function make($key)
    {
        return new Environment();
    }
}
