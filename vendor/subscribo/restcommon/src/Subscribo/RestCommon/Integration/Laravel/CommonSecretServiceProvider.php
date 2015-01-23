<?php namespace Subscribo\RestCommon\Integration\Laravel;

use Subscribo\RestCommon\CommonSecretProvider;
use Subscribo\ServiceProvider\ServiceProvider;

/**
 * Class CommonSecretServiceProvider
 *
 *
 * @package Subscribo\RestCommon
 */
class CommonSecretServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Subscribo\\RestCommon\\Interfaces\\CommonSecretProviderInterface', function () {
            $commonSecret = env('SUBSCRIBO_COMMON_SECRET');
            $commonSecretProvider = new CommonSecretProvider($commonSecret);
            return $commonSecretProvider;
        });
    }
}
