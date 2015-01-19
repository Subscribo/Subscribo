<?php namespace Subscribo\DependencyResolver\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Subscribo\DependencyResolver\DependencyResolver;


/**
 * Class DependencyResolverServiceProvider
 *
 * @package Subscribo\DependencyResolver
 */
class DependencyResolverServiceProvider extends ServiceProvider {

    protected $defer = true;

    public function register()
    {
        $this->app->bind('subscribo.dependencyresolver', function() {
            return new DependencyResolver;
        });
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\\DependencyResolver', 'Subscribo\\DependencyResolver\\Integration\\Laravel\\Facades\\DependencyResolver');
    }
}
