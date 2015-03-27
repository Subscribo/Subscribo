<?php namespace Subscribo\ApiClient\Integration\Laravel;

use Illuminate\Support\ServiceProvider;

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
        $this->forRouteRegistration[] = $this->app->register('\\Subscribo\\ApiClientAuth\\Integration\\Laravel\\ApiClientAuthServiceProvider');
        $this->app->register('\\Subscribo\\ApiClientOAuth\\Integration\\Laravel\\ApiClientOAuthServiceProvider');
        $this->app->register('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider');
        $this->forRouteRegistration[] = $this->app->register('\\Subscribo\\ApiClientLocalization\\Integration\\Laravel\\ApiClientLocalizationServiceProvider');
    }

    public function boot()
    {
        $router = $this->app->make('router');
        $csrf = class_exists('\\App\\Http\\Middleware\\VerifyCsrfToken') ? '\\App\\Http\\Middleware\\VerifyCsrfToken' : '\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken';
        $middleware = [
            '\\Subscribo\\Localization\\Middleware\\StandardLocaleToResponseHeader',
            '\\Subscribo\\Localization\\Middleware\\LocaleFromCookie',
            '\\Subscribo\\Localization\\Middleware\\LocaleFromSession',
            '\\Subscribo\\Localization\\Middleware\\LocaleToApp',
            $csrf
        ];
        foreach ($this->forRouteRegistration as $serviceProvider) {
            $serviceProvider->registerRoutes($router, $middleware, array());
        }
    }

}
