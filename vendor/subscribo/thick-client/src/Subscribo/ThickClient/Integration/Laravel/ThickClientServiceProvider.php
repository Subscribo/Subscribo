<?php

namespace Subscribo\ThickClient\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class ThickClientServiceProvider
 *
 * @package Subscribo\ThickClient
 */
class ThickClientServiceProvider extends ServiceProvider
{
    public function register()
    {

    }


    public function boot()
    {
        $this->registerMigrations();
    }

    /**
     * Publishes migrations
     */
    public function registerMigrations()
    {
        $packageMigrationsPath = $this->getPackagePath().'/install/database/migrations';
        $applicationMigrationsPath = $this->app->make('path.base').'/database/migrations';

        $this->publishes([$packageMigrationsPath => $applicationMigrationsPath], 'migrations');
    }


    public function registerIntegration()
    {
        $this->app->register('\\Subscribo\\ThickClientIntegration\\Managers\\ThickClientIntegrationServiceProvider');
    }
}
