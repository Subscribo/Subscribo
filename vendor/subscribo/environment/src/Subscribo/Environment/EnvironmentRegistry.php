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
        if ( ! isset(self::$_instances[$key])) {
            self::$_instances[$key] = self::make($key);
        }
        return self::$_instances[$key];
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
