<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;

/**
 * Class TransactionPluginDriverBase
 *
 * @package Subscribo\TransactionPluginManager
 */
abstract class TransactionPluginDriverBase implements TransactionPluginDriverInterface
{
    /**
     * @var \Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface
     */
    protected $pluginResourceManager;

    /**
     * @param PluginResourceManagerInterface $pluginResourceManager
     * @return $this|TransactionPluginDriverBase|TransactionPluginDriverInterface
     */
    public function withPluginResourceManager(PluginResourceManagerInterface $pluginResourceManager)
    {
        if ($this->pluginResourceManager === $pluginResourceManager) {

            return $this;
        }
        $instance = clone $this;
        $instance->pluginResourceManager = $pluginResourceManager;

        return $instance;
    }

    /**
     * @return PluginResourceManagerInterface
     */
    protected function getPluginResourceManager()
    {
        return $this->pluginResourceManager;
    }
}
