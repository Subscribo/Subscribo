<?php

namespace Subscribo\TransactionPluginManager\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class TransactionManagerPluginServiceProvider
 *
 * @package Subscribo\TransactionPluginManager
 */
class TransactionPluginManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('\\Subscribo\\TransactionPluginManager\\Interfaces\\TransactionDriverManagerInterface', '\\Subscribo\\TransactionPluginManager\\Managers\\TransactionDriverManager');
        $this->app->singleton('\\Subscribo\\TransactionPluginManager\\Interfaces\\PluginResourceManagerInterface', '\\Subscribo\\TransactionPluginManager\\Managers\\PluginResourceManager');
    }

    public function boot()
    {
        $this->registerTranslationResources(['questionary']);
    }
}
