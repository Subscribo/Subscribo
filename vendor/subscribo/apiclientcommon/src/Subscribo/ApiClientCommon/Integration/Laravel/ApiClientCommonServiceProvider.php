<?php namespace Subscribo\ApiClientCommon\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Subscribo\Support\Arr;

/**
 * Class ApiClientCommonServiceProvider
 *
 * @package Subscribo\ApiClientCommon
 */
class ApiClientCommonServiceProvider extends ServiceProvider
{

    protected $defer = false;

    protected $routesRegistered = false;

    public function register()
    {
        $this->app->register('Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider');
    }

    public function boot()
    {
        $packageDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $this->loadViewsFrom($packageDir.'/resources/views', 'subscribo');
        $this->publishes([
            $packageDir.'/resources/views/apiclientcommon/questionary.blade.php' => base_path('resources/views/vendor/subscribo/apiclientcommon/questionary.blade.php'),
            $packageDir.'/resources/views/apiclientcommon/question.blade.php' => base_path('resources/views/vendor/subscribo/apiclientcommon/question.blade.php'),
            $packageDir.'/resources/views/apiclientcommon/formerrors.blade.php' => base_path('resources/views/vendor/subscribo/apiclientcommon/formerrors.blade.php'),

        ], 'view');

        $this->app->make('view')->composer('subscribo::apiclientcommon.questionary', 'Subscribo\\ApiClientCommon\\ViewComposers\\QuestionaryComposer');
    }



    public function registerRoutes(Router $router, array $paths = array())
    {
        if ($this->routesRegistered) {
            return;
        }
        $defaultPaths = [
            'subscribo.serverRequest.questionary' => '/question',
            'subscribo.serverRequest.clientRedirect' => '/redirectback/{hash?}',
            'subscribo.generic.questionary' => '/question/{type}',
            'subscribo.generic.redirection' => '/redirection/{type}',
        ];
        $paths = Arr::mergeNatural($defaultPaths, $paths);
        $csrf = class_exists('\\App\\Http\\Middleware\\VerifyCsrfToken') ? '\\App\\Http\\Middleware\\VerifyCsrfToken' : '\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken';

        $router->get($paths['subscribo.serverRequest.questionary'], ['as' => 'subscribo.serverRequest.questionary', 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\QuestionaryController@getQuestionaryFromSession']);
        $router->post($paths['subscribo.serverRequest.questionary'], ['middleware' => [$csrf], 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\QuestionaryController@postQuestionary']);

        $router->get($paths['subscribo.serverRequest.clientRedirect'], ['as' => 'subscribo.serverRequest.clientRedirect', 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\ClientRedirectionController@getClientRedirectionRedirectingBack'])->where(['hash' => '[A-Za-z0-9]+']);

        $router->get($paths['subscribo.generic.questionary'], ['as' => 'subscribo.generic.questionary', 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\QuestionaryController@getQuestionaryByType'])->where(['type' => '[A-Za-z0-9]+']);
        $router->post($paths['subscribo.generic.questionary'], ['middleware' => [$csrf], 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\QuestionaryController@postQuestionary']);

        $router->get($paths['subscribo.generic.redirection'], ['as' => 'subscribo.generic.redirection', 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\ClientRedirectionController@getRedirectionByType'])->where(['type' => '[A-Za-z0-9]+']);

        $this->routesRegistered = true;
    }

}
