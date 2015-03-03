<?php namespace Subscribo\DependencyResolver;

use Subscribo\DependencyResolver\DependencyResolverInterface;
use Subscribo\DependencyResolver\CircularDependencyException;

/**
 * Class FlatDependencyResolver
 *
 * @package Subscribo\DependencyResolver
 */
class FlatDependencyResolver implements DependencyResolverInterface {


    /**
     * Resolves dependencies using flat search algorithm
     * Returns array of arrays with resolved dependencies (if possible)
     * i.e. ordered in such a fashion, that all values contained in an array are only those values,
     * which has been used previously as keys in the main array
     *
     * @param array $dependencies Array of arrays, values of the inner arrays refer to keys in main array
     * @return array Array of arrays with resolved dependencies
     * @throws \Subscribo\DependencyResolver\CircularDependencyException when circular dependency is found
     */
    public static function resolveFull(array $dependencies)
    {
        $resolved = array();
        $stack = $dependencies;
        while ($stack) {
            $newStack = array();
            foreach ($stack as $resolvingKey => $resolvingDependencies) {
                if (static::_isResolved($resolvingDependencies, $resolved)) {
                    $resolved[$resolvingKey] = $resolvingDependencies;
                } else {
                    $newStack[$resolvingKey] = $resolvingDependencies;
                }
            }
            if (count($newStack) >= count($stack)) {
                throw new CircularDependencyException("Circular dependency found among '".implode("', '", array_keys($stack))."'");
            }
            $stack = $newStack;
        }
        return $resolved;
    }

    /**
     * Checks, whether all items from dependencies list are among keys in resolved
     *
     * @param array $dependenciesList
     * @param array $resolved
     * @return bool
     */
    private static function _isResolved(array $dependenciesList, array $resolved)
    {
        foreach($dependenciesList as $dependency) {
            if ( ! array_key_exists($dependency, $resolved)) {
                return false;
            }
        }
        return true;
    }
}
