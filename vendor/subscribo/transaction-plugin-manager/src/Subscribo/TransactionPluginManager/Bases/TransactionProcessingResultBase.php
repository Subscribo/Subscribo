<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;

/**
 * Class TransactionProcessingResultBase
 *
 * @package Subscribo\TransactionPluginManager
 */
class TransactionProcessingResultBase implements TransactionProcessingResultInterface
{
    /**
     * @var \Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface
     */
    protected $transactionFacadeObject;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var null|string
     */
    protected $message;

    /**
     * @var bool
     */
    protected $registered;

    /**
     * @param TransactionFacadeInterface $transaction
     * @param string $status
     * @param string|null $message
     * @param bool $registered
     */
    public function __construct(TransactionFacadeInterface $transaction, $status, $message = null, $registered = false)
    {
        $this->transactionFacadeObject = $transaction;
        $this->status = $status;
        $this->message = $message;
        $this->registered = $registered;
    }

    /**
     * @return \Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface
     */
    public function getTransactionFacadeObject()
    {
        return $this->transactionFacadeObject;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * @return array
     */
    public function export()
    {
        return [
            'transaction' => $this->getTransactionFacadeObject()->getTransactionModelInstance(),
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'registered' => $this->isRegistered(),
        ];
    }
}
