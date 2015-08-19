<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;

/**
 * Abstract Class TransactionProcessorBase
 * @package Subscribo\TransactionPluginManager
 */
abstract class TransactionProcessorBase implements TransactionProcessorInterface
{

    /** @var \Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface */
    protected $driver;

    /** @var \Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface */
    protected $transaction;

    /**
     * @param TransactionPluginDriverInterface $driver
     * @param TransactionFacadeInterface $transaction
     */
    public function __construct(TransactionPluginDriverInterface $driver, TransactionFacadeInterface $transaction)
    {
        $this->driver = $driver;
        $this->transaction = $transaction;
    }

    /**
     * @return TransactionFacadeInterface
     */
    public function getTransactionFacade()
    {
        return $this->transaction;
    }

    /**
     * @return string
     */
    public function getDriverIdentifier()
    {
        return $this->driver->getDriverIdentifier();
    }

    /**
     * @return \Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface
     */
    protected function getLocalizer()
    {
        return $this->driver->getPluginResourceManager()->getLocalizer();
    }
}
