<?php namespace Subscribo\Localization\Integration\Laravel;

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
        $this->app->singleton('subscribo.localizer', '\\Subscribo\\Localization\\Localizer');
        $this->app->singleton('\\Subscribo\\Localization\\Interfaces\\LocalizerInterface', 'subscribo.localizer');
    }
}
