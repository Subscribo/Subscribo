<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

/**
 * Interface TransactionFacadeInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface TransactionFacadeInterface
{
    /**
     * @return \Subscribo\ModelCore\Models\Transaction|mixed
     */
    public function getTransactionModelInstance();

    /**
     * @return bool;
     */
    public function isChargeTransaction();
}
