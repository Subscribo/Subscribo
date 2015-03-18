<?php namespace Subscribo\Localization\Integration\Laravel;

use Subscribo\Localization\Localizer;
use Illuminate\Support\ServiceProvider;

/**
 * Class LocalizationServiceProvider
 *
 * @package Subscribo\Localization
 */
class LocalizationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('subscribo.localization.manager', '\\Subscribo\\Localization\\LocalizationManager');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizationManagerInterface', 'subscribo.localization.manager');
        $this->app->singleton('subscribo.localizer', function ($app) {
            return new Localizer($app->make('subscribo.localization.manager'), $app['config']['locale']);
        });
        $this->app->singleton('\\Subscribo\\Localization\\Localizer', 'subscribo.localizer');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizerInterface', 'subscribo.localizer');
    }
}
