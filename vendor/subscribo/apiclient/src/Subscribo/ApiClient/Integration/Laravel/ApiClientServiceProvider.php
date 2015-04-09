<?php namespace Subscribo\ApiClient\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class ApiClientServiceProvider
 *
 * @package Subscribo\ApiClient
 */
class ApiClientServiceProvider extends ServiceProvider {

    protected $defer = false;

    protected $forRouteRegistration = array();

    public function register()
    {
        $this->registerDependencies();
    }

    public function boot()
    {
        $this->registerRoutes();
    }

    public function registerRoutes()
    {
        $router = $this->getRouter();
        $csrf = class_exists('\\App\\Http\\Middleware\\VerifyCsrfToken') ? '\\App\\Http\\Middleware\\VerifyCsrfToken' : '\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken';
        $middleware = [
            '\\Subscribo\\Localization\\Middleware\\StandardLocaleToResponseHeader',
            '\\Subscribo\\Localization\\Middleware\\LocaleFromCookie',
            '\\Subscribo\\Localization\\Middleware\\LocaleFromSession',
            '\\Subscribo\\Localization\\Middleware\\LocaleToApp',
            $csrf
        ];
        foreach ($this->forRouteRegistration as $serviceProvider) {
            $serviceProvider->registerRoutes($middleware, array(), $router);
        }
    }

    protected function registerDependencies()
    {
        $this->forRouteRegistration[] = $this->registerServiceProvider('\\Subscribo\\ApiClientAuth\\Integration\\Laravel\\ApiClientAuthServiceProvider');
        $this->forRouteRegistration[] = $this->registerServiceProvider('\\Subscribo\\ApiClientOAuth\\Integration\\Laravel\\ApiClientOAuthServiceProvider');
        $this->forRouteRegistration[] = $this->registerServiceProvider('\\Subscribo\\ApiClientLocalization\\Integration\\Laravel\\ApiClientLocalizationServiceProvider');
        $this->app->register('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider');
    }

}
