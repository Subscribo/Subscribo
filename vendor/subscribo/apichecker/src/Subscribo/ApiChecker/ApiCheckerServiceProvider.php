<?php namespace Subscribo\ApiChecker;

use Subscribo\ServiceProvider\ServiceProvider;

/**
 * Class ApiCheckerServiceProvider
 *
 * @package Subscribo\ApiChecker
 */
class ApiCheckerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\ModelBase\\Integration\\Laravel\\ModelBaseServiceProvider');
    }

    public function boot()
    {
        $this->package('subscribo/apichecker');
        $configurationManager = $this->app->make('subscribo.config');
        $configDirectory = dirname(dirname(dirname(__FILE__))).'/config';
        $configurationManager->loadFileForPackage('apichecker', 'config', false, false, $configDirectory);
        $configurationManager->loadFileForPackage('apichecker', 'config', false, true, true);
        $uri = $configurationManager->getForPackage('apichecker', 'uri', 'checker');
        $this->app->make('router')->get($uri, function() {
            return $this->app->make('view')->make('apichecker::checker');
        });

    }
}
