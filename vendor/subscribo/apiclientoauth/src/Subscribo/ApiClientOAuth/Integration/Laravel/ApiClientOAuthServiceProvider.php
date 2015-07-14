<?php namespace Subscribo\ApiClientOAuth\Integration\Laravel;

use Subscribo\Support\ServiceProvider;
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
        $this->registerDependencies();

        $this->registerOAuthManager();
    }


    public function boot()
    {
        $this->registerResources();
    }


    public function registerRoutes(array $middleware, array $paths = array(), Router $router = null)
    {
        $defaultPaths = [
            'subscribo.oauth.login' => 'oauth/login/{driver}',
            'subscribo.oauth.handle' => 'oauth/handle/{driver}',
        ];
        $paths = array_replace($defaultPaths, $paths);
        $driverConstraint = ['driver' => '[A-Za-z0-9]+'];
        $router = $this->getRouter($router);

        $router->get($paths['subscribo.oauth.login'], [
            'as' => 'subscribo.oauth.login',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\ApiClientOAuth\\Controllers\\OAuthController@getLogin',
        ])->where($driverConstraint);

        $router->get($paths['subscribo.oauth.handle'], [
            'as' => 'subscribo.oauth.handle',
            'middleware' => $middleware,
            'uses' =>  '\\Subscribo\\ApiClientOAuth\\Controllers\\OAuthController@getHandle',
        ])->where($driverConstraint);
    }

    protected function registerDependencies()
    {
        $this->app->register('Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider');
    }


    protected function registerOAuthManager()
    {
        $this->app->bindIf(
            'Subscribo\\ApiClientOAuth\\OAuthManager',
            function ($app) {
                return new OAuthManager($app);
            },
            true
        );
    }

    protected function registerResources()
    {
        $this->registerTranslationResources('messages');

        $this->registerViews('LoginWithButtons');
    }

}
