<?php namespace Subscribo\ApiServer\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;


/**
 * Class ApiServerServiceProvider
 *
 * @package Subscribo\ApiServer
 */
class ApiServerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\RestCommon\\Integration\\Laravel\\CommonSecretServiceProvider');
        $this->app->register('\\Subscribo\\Api1\\Integration\\Laravel\\Api1ServiceProvider');
        $this->app->register('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider');


    }


}
