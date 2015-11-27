<?php

namespace Subscribo\Webshop\Integration\Laravel;

use Subscribo\Support\ServiceProvider;
use Illuminate\Routing\Router;

class WebshopServiceProvider extends ServiceProvider
{
    protected $routesRegistered = false;

    /**
     * @var \Subscribo\ApiClient\Integration\Laravel\ApiClientServiceProvider|null
     */
    protected $apiClientServiceProvider;

    public function register()
    {
        $apiClientServiceProviderClassName = '\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider';
        $this->apiClientServiceProvider = $this->registerServiceProvider($apiClientServiceProviderClassName);
    }


    public function boot()
    {
        $this->registerRoutes($this->apiClientServiceProvider->provideMiddleware());
        $this->registerViews();
        $this->registerTranslationResources('messages');
    }


    public function registerRoutes(array $middleware, array $paths = [], Router $router = null)
    {
        if ($this->routesRegistered) {
            return;
        }
        $defaultPaths = [
            'subscribo.webshop.product.list' => 'webshop/products',
            'subscribo.webshop.product.getBuy' => 'webshop/buy/{productId?}',
            'subscribo.webshop.product.postBuy' => 'webshop/buy/{productId?}',
            'subscribo.webshop.success' => 'webshop/success',

        ];
        $paths = array_replace($defaultPaths, $paths);
        $router = $this->getRouter($router);
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app->make('router');
        $router->get($paths['subscribo.webshop.product.list'], [
            'as' => 'subscribo.webshop.product.list',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\Webshop\\Http\\Controllers\\WebshopController@listProducts'
        ]);
        $router->get($paths['subscribo.webshop.product.getBuy'], [
            'as' => 'subscribo.webshop.product.getBuy',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\Webshop\\Http\\Controllers\\WebshopController@getBuyProduct'
        ])->where(['productId' => '[1-9][0-9]*']);
        $router->post($paths['subscribo.webshop.product.postBuy'], [
            'as' => 'subscribo.webshop.product.postBuy',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\Webshop\\Http\\Controllers\\WebshopController@postBuyProduct'
        ])->where(['productId' => '[1-9][0-9]*']);
        $router->get($paths['subscribo.webshop.success'], [
            'as' => 'subscribo.webshop.success',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\Webshop\\Http\\Controllers\\WebshopController@getSuccess'
        ]);

        $this->routesRegistered = true;
    }

}
