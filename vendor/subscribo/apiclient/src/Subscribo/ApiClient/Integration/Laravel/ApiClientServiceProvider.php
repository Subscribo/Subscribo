<?php namespace Subscribo\ApiClient\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;


/**
 * Class ApiClientServiceProvider
 *
 * @package Subscribo\ApiClient
 */
class ApiClientServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\RestCommon\\Integration\\Laravel\\CommonSecretServiceProvider');
        $this->app->register('\\Subscribo\\ApiClientAuth\\Integration\\Laravel\\ApiClientAuthServiceProvider');
        $this->app->register('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider');
        $this->app->register('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider');

    }


}
