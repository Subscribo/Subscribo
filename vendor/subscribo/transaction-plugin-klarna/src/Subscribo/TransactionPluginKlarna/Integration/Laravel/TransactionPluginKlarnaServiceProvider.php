<?php

namespace Subscribo\TransactionPluginKlarna\Integration\Laravel;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginServiceProviderBase;

/**
 * Class TransactionPluginKlarnaServiceProvider
 *
 * @package Subscribo\TransactionPluginKlarna
 */
class TransactionPluginKlarnaServiceProvider extends TransactionPluginServiceProviderBase
{
    public function getProvidedDrivers()
    {
        return [];
    }
}
