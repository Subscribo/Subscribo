<?php namespace Subscribo\ApiClientOAuth\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Subscribo\ApiClientOAuth\OAuthManager;


/**
 * Class ApiClientOAuthServiceProvider
 *
 * @package Subscribo\ApiClientOAuth
 */
class ApiClientOAuthServiceProvider extends ServiceProvider
{

    protected $defer = false;

    public function register()
    {
        $this->app->register('Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider');
        $this->app->bindIf(
            'Subscribo\\ApiClientOAuth\\OAuthManager',
            function ($app) {
                return new OAuthManager($app);
            },
            true
        );
    }

    public function boot()
    {
        $router = $this->app->make('router');
        $this->registerRoutes($router);
    }

    protected function registerRoutes(Router $router)
    {
        $router->get('oauth/login/{driver}', 'Subscribo\\ApiClientOAuth\\Controllers\\OAuthController@getLogin');
        $router->get('oauth/handle/{driver}', 'Subscribo\\ApiClientOAuth\\Controllers\\OAuthController@getHandle');
    }
}
