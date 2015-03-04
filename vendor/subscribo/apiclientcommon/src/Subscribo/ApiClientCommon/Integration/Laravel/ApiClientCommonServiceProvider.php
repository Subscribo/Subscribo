<?php namespace Subscribo\ApiClientCommon\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

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



    public function registerRoutes(Router $router, $uri = 'question')
    {
        if ($this->routesRegistered) {
            return;
        }
        $csrf = class_exists('\\App\\Http\\Middleware\\VerifyCsrfToken') ? '\\App\\Http\\Middleware\\VerifyCsrfToken' : '\\Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken';
        $router->get($uri, ['as' => 'subscribo.question', 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\QuestionaryController@getQuestionary']);
        $router->post($uri, ['middleware' => [$csrf], 'uses' => '\\Subscribo\\ApiClientCommon\\Controllers\\QuestionaryController@postQuestionary']);
        $this->routesRegistered = true;
    }

}
