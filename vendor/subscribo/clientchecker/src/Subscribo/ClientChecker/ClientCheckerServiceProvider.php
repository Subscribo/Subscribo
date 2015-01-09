<?php namespace Subscribo\ClientChecker;

use Subscribo\ServiceProvider\ServiceProvider;

/**
 * Class ClientCheckerServiceProvider
 *
 * @package Subscribo\ClientChecker
 */
class ClientCheckerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\ModelBase\\Integration\\Laravel\\ModelBaseServiceProvider');
    }

    public function boot()
    {
        $this->package('subscribo/clientchecker');
        $configurationManager = $this->app->make('subscribo.config');
        $configDirectory = dirname(dirname(dirname(__FILE__))).'/config';
        $configurationManager->loadFileForPackage('clientchecker', 'config', false, false, $configDirectory);
        $configurationManager->loadFileForPackage('clientchecker', 'config', false, true, true);
        $uri = $configurationManager->getForPackage('clientchecker', 'uri', 'client');
        $this->app->make('router')->get($uri, function() {
            return $this->app->make('view')->make('clientchecker::checker');
        });

    }
}
