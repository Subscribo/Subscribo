<?php

namespace Subscribo\ThinClientIntegration\Integration\Laravel;

use Subscribo\Support\ServiceProvider;
use Subscribo\ThinClientIntegration\Managers\ThinClientIntegrationManager;

/**
 * Class ThinClientIntegrationServiceProvider
 * @package Subscribo\ThinClientIntegration\Integration\Laravel
 */
class ThinClientIntegrationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('\\Subscribo\\ThinClientIntegration\\Managers\\ThinClientIntegrationManager',
            function ($app) {

                return new ThinClientIntegrationManager($app);
            }
        );

        $this->app->bind('\\Subscribo\\ClientIntegrationCommon\\Interfaces\\ClientIntegrationManagerInterface',
                         '\\Subscribo\\ThinClientIntegration\\Managers\\ThinClientIntegrationManager');
    }
}
