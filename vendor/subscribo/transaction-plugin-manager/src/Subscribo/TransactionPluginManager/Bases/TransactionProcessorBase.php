<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;

abstract class TransactionProcessorBase implements TransactionProcessorInterface
{
    /** @var \Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface  */
    protected $transaction;

    /**
     * @param TransactionFacadeInterface $transaction
     */
    public function __construct(TransactionFacadeInterface $transaction)
    {
        $this->transaction = $transaction;
    }
}
