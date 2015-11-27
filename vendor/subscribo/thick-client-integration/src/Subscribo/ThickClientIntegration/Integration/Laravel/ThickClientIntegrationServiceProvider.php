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
        $this->app->register('\\Subscribo\\Api1Connector\\Integration\\Laravel\\Api1ConnectorServiceProvider');
        $this->app->singleton('\\Subscribo\\ThickClientIntegration\\Managers\\ThickClientIntegrationManager',
            function ($app) {

                return new ThickClientIntegrationManager($app);
            }
        );

        $this->app->bind('\\Subscribo\\ClientIntegrationCommon\\Interfaces\\ClientIntegrationManagerInterface',
                         '\\Subscribo\\ThickClientIntegration\\Managers\\ThickClientIntegrationManager');
    }
}
