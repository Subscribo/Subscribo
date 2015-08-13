<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface PluginResourceManagerInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface PluginResourceManagerInterface
{
    /**
     * Returns specified driver, connected to this instance of PluginResourceManagerInterface as its resource manager
     *
     * @param TransactionPluginDriverInterface|string $name
     * @return TransactionPluginDriverInterface
     */
    public function getDriver($name);

    /**
     * @return LocalizerFacadeInterface
     */
    public function getLocalizer();

    /**
     * @return LoggerInterface
     */
    public function getLogger();
}
