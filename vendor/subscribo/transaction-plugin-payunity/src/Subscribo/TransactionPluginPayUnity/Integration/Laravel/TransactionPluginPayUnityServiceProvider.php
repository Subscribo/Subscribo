<?php

namespace Subscribo\TransactionPluginPayUnity\Integration\Laravel;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginServiceProviderBase;
use Subscribo\TransactionPluginPayUnity\Drivers\CopyAndPayDriver;
use Subscribo\TransactionPluginPayUnity\Drivers\PostDriver;

/**
 * Class TransactionPluginPayUnityServiceProvider
 *
 * @package Subscribo\TransactionPluginPayUnity
 */
class TransactionPluginPayUnityServiceProvider extends TransactionPluginServiceProviderBase
{
    public function getProvidedDrivers()
    {
        return [
            CopyAndPayDriver::getDriverIdentifier() => '\\Subscribo\\TransactionPluginPayUnity\\Drivers\\CopyAndPayDriver',
            PostDriver::getDriverIdentifier() => '\\Subscribo\\TransactionPluginPayUnity\\Drivers\\PostDriver'
        ];
    }
}
