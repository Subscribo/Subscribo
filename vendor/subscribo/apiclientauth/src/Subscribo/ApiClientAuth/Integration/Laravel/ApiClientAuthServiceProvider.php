<?php namespace Subscribo\ApiClientAuth\Integration\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class ApiClientAuthServiceProvider
 *
 * @package Subscribo\ApiClientAuth
 */
class ApiClientAuthServiceProvider extends ServiceProvider
{
    protected $forRouteRegistration = array();

    public function register()
    {
        $this->app->register('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider');
        $this->forRouteRegistration[] = $this->app->register('Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider');
    }

    public function boot()
    {
        $this->app->make('auth')->extend('remote', function ($app) {
            return $app->make('Subscribo\\ApiClientAuth\\RemoteAccountProvider');
        });
        $router = $this->app->make('router');
        foreach ($this->forRouteRegistration as $serviceProvider) {
            $serviceProvider->registerRoutes($router);
        }

    }
}
