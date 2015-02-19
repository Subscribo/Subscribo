<?php namespace Subscribo\RestProxy\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\AliasLoader;


/**
 * Class RestProxyServiceProvider
 *
 * @package Subscribo\RestProxy
 */
class RestProxyServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider');
        $this->app->register('Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider');

        $this->app->singleton('subscribo.restproxy', 'Subscribo\\RestProxy\\RestProxy');

        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Subscribo\\RestProxy', 'Subscribo\\RestProxy\\Integration\\Laravel\\Facades\\RestProxy');
    }

    public function boot()
    {
        /** @var \Subscribo\RestProxy\RestProxy $instance */
        $instance = $this->app->make('subscribo.restproxy');
        $configDir = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config';
        /** @var \Subscribo\Config $configManager  */
        $configManager = $this->app->make('subscribo.config');
        $configManager->loadFileForPackage('restproxy', 'default', true, false, $configDir);
        $configManager->loadFileForPackage('restproxy', 'default', true, true, true);
        $settings = $configManager->getForPackage('restproxy', 'default', array());

        $instance->setup($settings);
        $uriBase = $configManager->getForPackage('restproxy', 'default.uri', 'proxy');
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app->make('router');
        $router->any($uriBase.'/{uri?}',
            [
                'middleware' => 'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken',
                function($uri = null) use ($instance) {
                    return $instance->call($uri);
                },
            ]
        )->where('uri', '.*');
    }
}
