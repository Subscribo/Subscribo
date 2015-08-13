<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;

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
}
