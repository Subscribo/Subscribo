<?php

namespace Subscribo\CheckoutPlugin\Integration\Laravel;

use Subscribo\Support\ServiceProvider;
use Illuminate\Routing\Router;

/**
 * Class CheckoutPluginServiceProvider
 *
 * @package Subscribo\CheckoutPlugin
 */
class CheckoutPluginServiceProvider extends ServiceProvider
{
    protected $routesRegistered = false;

    /**
     * @var \Subscribo\ApiClient\Integration\Laravel\ApiClientServiceProvider|null
     */
    protected $apiClientServiceProvider;

    public function register()
    {
        $this->app->register('\\Subscribo\\ClientCheckoutCommon\\Integration\\Laravel\\ClientCheckoutCommonServiceProvider');
        $apiClientServiceProviderClassName = '\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider';
        $this->apiClientServiceProvider = $this->registerServiceProvider($apiClientServiceProviderClassName);
    }


    public function boot()
    {
        $this->registerRoutes($this->apiClientServiceProvider->provideMiddleware());
        $this->registerViews([], true, true, 'subscribo-checkout-plugin-default-views');
        $this->registerTranslationResources('messages');
    }


    public function registerRoutes(array $middleware, array $paths = [], Router $router = null)
    {
        if ($this->routesRegistered) {
            return;
        }
        $defaultPaths = [
            'subscribo.plugin.checkout.product.list' => 'subscribo/plugin/checkout/products',
            'subscribo.plugin.checkout.product.getBuy' => 'subscribo/plugin/checkout/buy/{productId?}',
            'subscribo.plugin.checkout.product.postBuy' => 'subscribo/plugin/checkout/buy/{productId?}',
            'subscribo.plugin.checkout.success' => 'subscribo/plugin/checkout/success',

        ];
        $paths = array_replace($defaultPaths, $paths);
        $router = $this->getRouter($router);
        $router->get($paths['subscribo.plugin.checkout.product.list'], [
            'as' => 'subscribo.plugin.checkout.product.list',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\CheckoutPlugin\\Http\\Controllers\\CheckoutPluginController@listProducts'
        ]);
        $router->get($paths['subscribo.plugin.checkout.product.getBuy'], [
            'as' => 'subscribo.plugin.checkout.product.getBuy',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\CheckoutPlugin\\Http\\Controllers\\CheckoutPluginController@getBuyProduct'
        ])->where(['productId' => '[1-9][0-9]*']);
        $router->post($paths['subscribo.plugin.checkout.product.postBuy'], [
            'as' => 'subscribo.plugin.checkout.product.postBuy',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\CheckoutPlugin\\Http\\Controllers\\CheckoutPluginController@postBuyProduct'
        ])->where(['productId' => '[1-9][0-9]*']);
        $router->get($paths['subscribo.plugin.checkout.success'], [
            'as' => 'subscribo.plugin.checkout.success',
            'middleware' => $middleware,
            'uses' => '\\Subscribo\\CheckoutPlugin\\Http\\Controllers\\CheckoutPluginController@getSuccess'
        ]);

        $this->routesRegistered = true;
    }
}
