<?php namespace Subscribo\ApiClientAuth\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;

/**
 * Class ApiClientAuthServiceProvider
 *
 * @package Subscribo\ApiClientAuth
 */
class ApiClientAuthServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->register('Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider');
    }

    public function boot()
    {
        $this->app->make('auth')->extend('remote', function ($app) {
            return $app->make('Subscribo\\ApiClientAuth\\RemoteAccountProvider');
        });

    }
}
