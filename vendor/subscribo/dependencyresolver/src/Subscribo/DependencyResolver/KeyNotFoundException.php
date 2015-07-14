<?php namespace Subscribo\DependencyResolver;

use Subscribo\DependencyResolver\DependencyResolverException;

/**
 * Class KeyNotFoundException to be used in DependencyResolver::reorder()
 *
 * @package Subscribo\DependencyResolver
 */
class KeyNotFoundException extends DependencyResolverException {}
