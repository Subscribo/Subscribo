<?php namespace Subscribo\Exception\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Subscribo\Environment\EnvironmentRegistry;


/**
 * Class EnvironmentServiceProvider
 *
 * @package Subscribo\Environment
 */
class ApiExceptionHandlerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->singleton('Subscribo\\Exception\\Handlers\\ApiExceptionHandler');
        $this->app->singleton('subscribo.exceptionhandler', 'Subscribo\\Exception\\Handlers\\ApiExceptionHandler');
        $this->app->singleton('Subscribo\\Exception\\Interfaces\\ExceptionHandlerInterface','Subscribo\\Exception\\Handlers\\ApiExceptionHandler');

        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\\ApiExceptionHandler', 'Subscribo\\Exception\\Integration\\Laravel\\Facades\\ApiExceptionHandler');
    }
}
