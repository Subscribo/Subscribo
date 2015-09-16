<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;

/**
 * Interface TransactionProcessorInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface TransactionProcessorInterface
{
    /**
     * @return TransactionProcessingResultInterface
     */
    public function process();

    /**
     * @return string
     */
    public function getDriverIdentifier();

    /**
     * @return TransactionFacadeInterface
     */
    public function getTransactionFacade();
}
