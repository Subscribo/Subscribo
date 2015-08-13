<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;

/**
 * Interface TransactionPluginChargeDriverInterface for transaction drivers, capable of processing charge transaction
 * (Charge transactions are transactions, when customer is debited and merchant credited specific amount
 * e.g. customer is paying for ordered products or services)
 *
 * @package Subscribo\TransactionPluginManager
 */
interface TransactionPluginChargeDriverInterface extends TransactionPluginDriverInterface
{

}
