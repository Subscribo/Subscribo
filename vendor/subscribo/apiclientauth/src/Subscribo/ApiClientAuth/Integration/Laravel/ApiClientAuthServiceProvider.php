<?php namespace Subscribo\ApiClientAuth\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class ApiClientAuthServiceProvider
 *
 * @package Subscribo\ApiClientAuth
 */
class ApiClientAuthServiceProvider extends ServiceProvider
{
    /** @var array  */
    protected $forRouteRegistration = array();

    /** @var bool  */
    protected $routesRegistered = false;


    public function register()
    {
        $this->registerDependencies();
    }


    public function boot()
    {
        $this->extendAuthWithRemoteDriver();

        $this->registerTranslationResources('messages');
    }


    public function registerRoutes(array $middleware, array $paths = array(), $router = null)
    {
        if ($this->routesRegistered) {
            return;
        }
        foreach ($this->forRouteRegistration as $serviceProvider) {
            $serviceProvider->registerRoutes($middleware, $paths, $router);
        }
        $this->routesRegistered = true;
    }


    public function extendAuthWithRemoteDriver()
    {
        $this->app->make('auth')->extend('remote', function ($app) {
            return $app->make('Subscribo\\ApiClientAuth\\RemoteAccountProvider');
        });
    }


    protected function registerDependencies()
    {
        $this->app->register('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider');
        $this->forRouteRegistration[] = $this->registerServiceProvider('Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider');
    }
}
