<?php

namespace Subscribo\TransactionPluginKlarna\Drivers;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginDriverBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginKlarna\Processors\InvoiceProcessor;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginChargeDriverInterface;

/**
 * Class InvoiceDriver
 *
 * @package Subscribo\TransactionPluginKlarna
 */
class InvoiceDriver extends TransactionPluginDriverBase implements TransactionPluginChargeDriverInterface
{
    const DRIVER_IDENTIFIER = 'subscribo/klarna-invoice';

    public static function getDriverIdentifier()
    {
        return static::DRIVER_IDENTIFIER;
    }

    /**
     * @param TransactionFacadeInterface $transaction
     * @return InvoiceProcessor
     */
    public function makeProcessor(TransactionFacadeInterface $transaction)
    {
        return new InvoiceProcessor($this, $transaction);
    }
}
