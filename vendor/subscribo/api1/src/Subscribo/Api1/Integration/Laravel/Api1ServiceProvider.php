<?php namespace Subscribo\Api1\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Subscribo\Api1\ControllerRegistrar;

/**
 * Class Api1ServiceProvider
 *
 * @package Subscribo\Api1
 */
class Api1ServiceProvider extends ServiceProvider
{

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\Auth\\Integration\\Laravel\\AuthServiceProvider');

        $middleware = ['Subscribo\\Auth\\Middleware\\ApiAuth'];
        $options = ['middleware' => $middleware];
        $controllerRegistrar = new ControllerRegistrar($this->app->make('router'), '/api/v1', $options);
        $controllers = [
            'Subscribo\\Api1\\Controllers\\AccountController',
        ];
        $controllerRegistrar->registerControllers($controllers);
        $controllerRegistrar->addInfoRoute(['version' => 1]);
    }
}
