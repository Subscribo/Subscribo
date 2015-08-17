<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;

/**
 * Interface TransactionProcessingResultInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface TransactionProcessingResultInterface
{
    const STATUS_SUCCESS = 'success';
    const STATUS_WAITING = 'waiting';
    const STATUS_OWN_RISK = 'own_risk';
    const STATUS_FAILURE = 'failure';
    const STATUS_ERROR = 'error';

    /**
     * @return TransactionFacadeInterface
     */
    public function getTransactionFacadeObject();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string|null
     */
    public function getMessage();

    /**
     * @return bool
     */
    public function isRegistered();

    /**
     * @return bool
     */
    public function shouldContinue();

    /**
     * @return array
     */
    public function export();
}
