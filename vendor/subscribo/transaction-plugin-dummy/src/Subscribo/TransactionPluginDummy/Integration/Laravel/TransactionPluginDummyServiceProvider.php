<?php

namespace Subscribo\TransactionPluginDummy\Integration\Laravel;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginServiceProviderBase;
use Subscribo\TransactionPluginDummy\Drivers\SuccessForAllDriver;

/**
 * Class TransactionPluginDummyServiceProvider
 *
 * @package Subscribo\TransactionPluginDummy
 */
class TransactionPluginDummyServiceProvider extends TransactionPluginServiceProviderBase
{
    /**
     * @return array
     */
    public function getProvidedDrivers()
    {
        return [
            SuccessForAllDriver::getDriverIdentifier() => '\\Subscribo\\TransactionPluginDummy\\Drivers\\SuccessForAllDriver',
        ];
    }
}
