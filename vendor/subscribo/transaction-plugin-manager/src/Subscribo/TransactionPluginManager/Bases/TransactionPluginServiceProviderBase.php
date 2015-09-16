<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Subscribo\Support\ServiceProvider;

/**
 * Class TransactionPluginServiceProviderBase
 *
 * @package Subscribo\TransactionPluginManager
 */
abstract class TransactionPluginServiceProviderBase extends ServiceProvider
{
    protected $transactionPluginManagerServiceProvider;

    /**
     * @return array
     */
    public abstract function getProvidedDrivers();


    public function register()
    {
        $this->transactionPluginManagerServiceProvider = $this->registerServiceProvider('\\Subscribo\\TransactionPluginManager\\Integration\\Laravel\\TransactionPluginManagerServiceProvider');
    }


    public function boot()
    {
        /** @var \Subscribo\TransactionPluginManager\Interfaces\TransactionDriverManagerInterface $manager */
        $manager = $this->app->make('\\Subscribo\\TransactionPluginManager\\Interfaces\\TransactionDriverManagerInterface');
        $drivers = $this->getProvidedDrivers();
        foreach ($drivers as $key => $driver)
        {
            $manager->registerDriver($key, $driver);
        }
    }
}
