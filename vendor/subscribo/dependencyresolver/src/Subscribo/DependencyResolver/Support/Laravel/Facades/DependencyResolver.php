<?php namespace Subscribo\DependencyResolver\Support\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class DependencyResolver
 *
 * @package Subscribo\DependencyResolver
 */
class DependencyResolver extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'subscribo.dependencyresolver';

    }

}
