<?php namespace Subscribo\Support\Fixes;

/**
 * Abstract class ServiceProvider
 * Extends original Laravel ServiceProvider and fixes some functionality
 *
 * @package Subscribo\Support
 */
abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Fix for original method publishes allowing tags from different ServiceProviders to be merged together
     * Written with look to original method
     *
     * @param array $paths
     * @param string|null $group
     */
    public function publishes(array $paths, $group = null)
    {
        if ($group) {
            $tmpGroupContent = empty(static::$publishGroups[$group]) ? array() : static::$publishGroups[$group];
        }
        parent::publishes($paths, $group);
        if ($group) {
            static::$publishGroups[$group] = array_replace($tmpGroupContent, $paths);
        }

    }

    /**
     * Fix for original method publishes allowing tags from different ServiceProviders to be merged together
     * Written with look to original method
     *
     * @param string|null $provider
     * @param string|null $group
     * @return array
     */
    public static function pathsToPublish($provider = null, $group = null)
    {
        if ($provider and $group) {
            if (empty(static::$publishes[$provider])) {
                return array();
            }
            if (empty(static::$publishGroups[$group])) {
                return array();
            }
            return array_intersect(static::$publishes[$provider], static::$publishGroups[$group]);
        }
        return parent::pathsToPublish($provider, $group);
    }

}
