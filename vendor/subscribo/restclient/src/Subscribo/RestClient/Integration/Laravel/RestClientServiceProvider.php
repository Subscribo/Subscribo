<?php namespace Subscribo\RestClient\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;


/**
 * Class RestClientServiceProvider
 *
 * @package Subscribo\RestClient
 */
class RestClientServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->singleton('Subscribo\\RestClient\\RestClient');
        $this->app->singleton('subscribo.restclient', 'Subscribo\\RestClient\\RestClient');
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\RestClient', 'Subscribo\\RestClient\\Integration\\Laravel\\Facades\\RestClient');
    }

    public function boot()
    {
        $configDir = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config';
        /** @var \Subscribo\Config $configManager  */
        $configManager = $this->app->make('subscribo.config');
        $configManager->loadFileForPackage('restclient', 'default', true, false, $configDir);
        $configManager->loadFileForPackage('restclient', 'default', true, true, true);
        $settings = $configManager->getForPackage('restclient', 'default', array());
        $this->app->make('subscribo.restclient')->setup($settings);
    }
}
