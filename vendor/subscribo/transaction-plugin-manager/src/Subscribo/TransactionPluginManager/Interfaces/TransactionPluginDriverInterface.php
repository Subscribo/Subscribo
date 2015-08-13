<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;

/**
 * Interface TransactionPluginDriverInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface TransactionPluginDriverInterface
{
    /**
     * @param TransactionFacadeInterface $transaction
     * @return TransactionProcessorInterface
     */
    public function makeProcessor(TransactionFacadeInterface $transaction);

    /**
     * @param PluginResourceManagerInterface $pluginManager
     * @return TransactionPluginDriverInterface
     */
    public function withPluginResourceManager(PluginResourceManagerInterface $pluginManager);
}
