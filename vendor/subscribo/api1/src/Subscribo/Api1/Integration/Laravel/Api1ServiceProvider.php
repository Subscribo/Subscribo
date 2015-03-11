<?php namespace Subscribo\Api1\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
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

    }

    public function boot()
    {
        $this->registerControllers();
    }

    protected function registerControllers()
    {
        $middleware = [
            'Subscribo\\Auth\\Middleware\\ApiAuth',
        ];
        $options = ['middleware' => $middleware];
        $controllerRegistrar = new ControllerRegistrar($this->app->make('router'), '/api/v1', $options);
        $controllers = [
            'Subscribo\\Api1\\Controllers\\AccountController',
            'Subscribo\\Api1\\Controllers\\OAuthController',
            'Subscribo\\Api1\\Controllers\\AnswerController',
        ];
        $controllerRegistrar->registerControllers($controllers);
        $controllerRegistrar->addInfoRoute(['version' => 1]);
    }
}
