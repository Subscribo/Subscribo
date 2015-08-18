<?php

namespace Subscribo\TransactionPluginKlarna\Integration\Laravel;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginServiceProviderBase;
use Subscribo\TransactionPluginKlarna\Drivers\InvoiceDriver;

/**
 * Class TransactionPluginKlarnaServiceProvider
 *
 * @package Subscribo\TransactionPluginKlarna
 */
class TransactionPluginKlarnaServiceProvider extends TransactionPluginServiceProviderBase
{
    public function getProvidedDrivers()
    {
        return [
            InvoiceDriver::getDriverIdentifier() => '\\Subscribo\\TransactionPluginKlarna\\Drivers\\InvoiceDriver',
        ];
    }
}
