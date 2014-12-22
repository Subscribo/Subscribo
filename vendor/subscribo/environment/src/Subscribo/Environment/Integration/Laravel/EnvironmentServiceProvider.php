<?php namespace Subscribo\Environment\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Subscribo\Environment\EnvironmentRegistry;


/**
 * Class EnvironmentServiceProvider
 *
 * @package Subscribo\Environment
 */
class EnvironmentServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $environmentInstance = EnvironmentRegistry::getInstance();
        $this->app->instance('subscribo.environment', $environmentInstance);
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\Environment', 'Subscribo\\Environment\\Integration\\Laravel\\Facades\\Environment');
    }
}
