<?php

namespace Subscribo\ThickClientIntegration\Integration\Laravel;

use Subscribo\Support\ServiceProvider;
use Subscribo\ThickClientIntegration\Managers\ThickClientIntegrationManager;

/**
 * Class ThickClientIntegrationServiceProvider
 *
 * @package Subscribo\ThickClientIntegration
 */
class ThickClientIntegrationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('\\Subscribo\\ThickClientIntegration\\Managers\\ThickClientIntegrationManager',
            function ($app) {

                return new ThickClientIntegrationManager($app);
            }
        );

        $this->app->bind('\\Subscribo\\ClientIntegrationCommon\\Interfaces\\ClientIntegrationManagerInterface',
                         '\\Subscribo\\ThickClientIntegration\\Managers\\ThickClientIntegrationManager');
    }
}
