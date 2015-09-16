<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Closure;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;

/**
 * Interface TransactionDriverManagerInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface TransactionDriverManagerInterface
{
    /**
     * @param string $identifier
     * @param string|TransactionPluginDriverInterface|Closure $driver
     * @return $this
     */
    public function registerDriver($identifier, $driver);

    /**
     * @param string $identifier
     * @return TransactionPluginDriverInterface
     */
    public function getDriver($identifier);
}
