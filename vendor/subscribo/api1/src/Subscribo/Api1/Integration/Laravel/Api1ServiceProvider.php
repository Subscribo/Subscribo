<?php namespace Subscribo\Api1\Integration\Laravel;

use Subscribo\Support\ServiceProvider;
use Subscribo\Api1\ControllerRegistrar;
use Subscribo\Api1\Factories\LocaleSettingsFactory;
use Subscribo\Localization\Interfaces\LocalizationResourcesManagerInterface;
use Subscribo\Localization\Integration\Laravel\LocalizationServiceProvider;


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
        if (class_exists('\\Subscribo\\TransactionPluginManager\\Integration\\Laravel\\TransactionPluginManagerServiceProvider')) {
            $this->app->register('\\Subscribo\\TransactionPluginManager\\Integration\\Laravel\\TransactionPluginManagerServiceProvider');
        }
        $this->app->register('\\Subscribo\\Auth\\Integration\\Laravel\\AuthServiceProvider');
        /** @var LocalizationServiceProvider $localizationServiceProvider */
        $localizationServiceProvider = $this->app->register('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider');
        if (true === $localizationServiceProvider) {
            $localizationServiceProvider = $this->app->getProvider('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider');
        }
        $localizationServiceProvider->registerLocaleSettingsManager(array(), null, '\\Subscribo\\Api1\\Factories\\LocaleSettingsFactory', true);
        $this->app->register('\\Subscribo\\ValidationLocalization\\Integration\\Laravel\\ValidationLocalizationServiceProvider');
        $this->app->register('\\Subscribo\\ApiServerJob\\Integration\\Laravel\\ApiServerJobServiceProvider');
    }

    public function boot()
    {
        $this->registerControllers();
        $this->registerTranslationResources(['questionary', 'controllers']);
    }

    protected function registerControllers()
    {
        $middleware = [
            'Subscribo\\Localization\\Middleware\\StandardLocaleToResponseHeader',
            'Subscribo\\Localization\\Middleware\\LocaleFromHeader',
            'Subscribo\\Localization\\Middleware\\LocaleToApp',
            'Subscribo\\Auth\\Middleware\\ApiAuth',
            'Subscribo\\Api1\\Middleware\\Logging',
        ];
        $options = ['middleware' => $middleware];
        $controllerRegistrar = new ControllerRegistrar($this->app->make('router'), '/api/v1', $options);
        $controllers = [
            'Subscribo\\Api1\\Controllers\\AccountController',
            'Subscribo\\Api1\\Controllers\\OAuthController',
            'Subscribo\\Api1\\Controllers\\AnswerController',
            'Subscribo\\Api1\\Controllers\\BusinessController',
            'Subscribo\\Api1\\Controllers\\TransactionController',
        ];
        $controllerRegistrar->registerControllers($controllers);
        $controllerRegistrar->addInfoRoute(['version' => 1]);
    }
}
