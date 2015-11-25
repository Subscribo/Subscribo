<?php

namespace Subscribo\IntegrationToSpark\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

class IntegrationToSparkServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register('\\Subscribo\\ThickClient\\Integration\\Laravel\\ThickClientServiceProvider');
        $this->app->register('\\Subscribo\\ThickClientIntegration\\Integration\\Laravel\\ThickClientIntegrationServiceProvider');
        $this->app->register('\\Subscribo\\Webshop\\Integration\\Laravel\\WebshopServiceProvider');
    }

    public function boot()
    {
        $this->registerViews();
    }
}
