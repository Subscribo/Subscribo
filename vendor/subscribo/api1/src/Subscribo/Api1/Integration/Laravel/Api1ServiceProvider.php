<?php namespace Subscribo\Api1\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use Subscribo\Api1\ControllerRegistrar;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;

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
        $this->app->register('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider');
    }

    public function boot()
    {
        $this->registerControllers();
        $this->registerLocalizationResources();
    }

    protected function registerControllers()
    {
        $middleware = [
            'Subscribo\\Localization\\Middleware\\LocaleFromHeader',
            'Subscribo\\Auth\\Middleware\\ApiAuth',
            'Subscribo\\Api1\\Middleware\\Logging',
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

    protected function registerLocalizationResources()
    {
        $packagePath = dirname(dirname(dirname(dirname(dirname(__DIR__)))));

        /** @var LocalizationManagerInterface $manager */
        $manager = $this->app->make('subscribo.localization.manager');
        $manager->registerNamespace('api1', $packagePath.'/resources/lang', 'questionary');

    }
}
