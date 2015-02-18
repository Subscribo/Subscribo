<?php namespace Subscribo\ApiClient\Integration\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class ApiClientServiceProvider
 *
 * @package Subscribo\ApiClient
 */
class ApiClientServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider');
        $this->app->register('\\Subscribo\\ApiClientAuth\\Integration\\Laravel\\ApiClientAuthServiceProvider');
        $this->app->register('\\Subscribo\\ApiClientOAuth\\Integration\\Laravel\\ApiClientOAuthServiceProvider');
        $this->app->register('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider');
    }

}
