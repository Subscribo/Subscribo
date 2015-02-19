<?php namespace Subscribo\Config\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Subscribo\Config\Config;


/**
 * Class ConfigServiceProvider
 *
 * @package Subscribo\Config
 */
class ConfigServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\Environment\\Integration\\Laravel\\EnvironmentServiceProvider');
        $this->app->singleton('subscribo.config', function() {
            return new Config($this->app->make('subscribo.environment'), ($this->app->make('path.base')));
        });
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\\Config', 'Subscribo\\Config\\Integration\\Laravel\\Facades\\Config');
    }
}
