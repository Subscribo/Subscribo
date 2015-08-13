<?php

namespace Subscribo\TransactionPluginPayUnity\Integration\Laravel;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginServiceProviderBase;

class TransactionPluginPayUnityServiceProvider extends TransactionPluginServiceProviderBase
{
    public function getProvidedDrivers()
    {
        return [];
    }
}
