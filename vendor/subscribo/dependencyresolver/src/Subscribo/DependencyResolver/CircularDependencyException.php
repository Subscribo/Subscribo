<?php namespace Subscribo\DependencyResolver;

use Subscribo\DependencyResolver\DependencyResolverException;

/**
 * Class CircularDependencyException to be thrown when circular dependency is found
 *
 * @package Subscribo\DependencyResolver
 */
class CircularDependencyException extends DependencyResolverException {}
