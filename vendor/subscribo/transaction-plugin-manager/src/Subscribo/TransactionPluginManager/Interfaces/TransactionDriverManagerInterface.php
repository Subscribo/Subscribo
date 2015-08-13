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
     * @param string $name
     * @param string|TransactionPluginDriverInterface|Closure $driver
     * @return $this
     */
    public function registerDriver($name, $driver);

    /**
     * @param string $name
     * @return TransactionPluginDriverInterface
     */
    public function getDriver($name);
}
