<?php

namespace Subscribo\DependencyResolver;

/**
 * Interface DependencyResolverInterface for DependencyResolver implementation (different resolving algorithms)
 *
 * @package Subscribo\DependencyResolver
 */
interface DependencyResolverInterface
{
    /**
     * Returns array of arrays with resolved dependencies (if possible)
     * i.e. ordered in such a fashion, that all values contained in an array are only those values,
     * which has been used previously as keys in the main array
     *
     * @param array $dependencies Array of arrays, values of the inner arrays refer to keys in main array
     * @return array Array of arrays with resolved dependencies
     * @throws \Subscribo\DependencyResolver\CircularDependencyException when circular dependency is found
     */
    public static function resolveFull(array $dependencies);
}
