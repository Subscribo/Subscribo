<?php namespace Subscribo\Modelbase\Integration\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class ModelBaseServiceProvider
 *
 * @package Subscribo\Modelbase
 */
class ModelBaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            'Subscribo\\TranslatableModel\\Interfaces\\LocaleConfigurationInterface',
            'Subscribo\\ModelBase\\LocalizerLocaleConfiguration'
        );
    }

}
