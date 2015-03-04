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

        $packageDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $this->loadViewsFrom($packageDir.'/resources/views', 'subscribo');
        $this->publishes([
            $packageDir.'/resources/views/apiclientoauth/loginwithbuttons.blade.php'
                => base_path('resources/views/vendor/subscribo/apiclientoauth/loginwithbuttons.blade.php'),
        ], 'view');
        $this->app->make('view')->composer('subscribo::apiclientoauth.loginwithbuttons', 'Subscribo\\ApiClientOAuth\\ViewComposers\\LoginWithButtonsComposer');
    }

    protected function registerRoutes(Router $router)
    {
        $router->get('oauth/login/{driver}', ['as' => 'subscribo.oauth.login', 'uses' => '\\Subscribo\\ApiClientOAuth\\Controllers\\OAuthController@getLogin']);
        $router->get('oauth/handle/{driver}', ['as' => 'subscribo.oauth.handle', 'uses' =>  '\\Subscribo\\ApiClientOAuth\\Controllers\\OAuthController@getHandle']);
    }
}
