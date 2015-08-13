<?php

namespace Subscribo\TransactionPluginManager\Managers;

use RuntimeException;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginChargeDriverInterface;
use Subscribo\TransactionPluginManager\Facades\TransactionFacade;
use Subscribo\ModelCore\Models\Transaction;


class TransactionProcessingManager
{
    /** @var PluginResourceManagerInterface  */
    protected $transactionPluginManager;

    /** @var \Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface  */
    protected $driver;

    public function __construct(PluginResourceManagerInterface $transactionPluginManager, $driver)
    {
        $this->transactionPluginManager = $transactionPluginManager;
        $this->driver = $transactionPluginManager->getDriver($driver);
    }

    public function charge(Transaction $transaction)
    {
        if ( ! ($this->driver instanceof TransactionPluginChargeDriverInterface)) {

            throw new RuntimeException('Provided driver is not able to process charge transactions');
        }
        $transactionFacade = new TransactionFacade($transaction);
        if ( ! $transactionFacade->isChargeTransaction()) {
            throw new RuntimeException('Provided transaction is not charge');
        }

        return $this->driver->makeProcessor($transactionFacade)->process()->export();
    }
}
