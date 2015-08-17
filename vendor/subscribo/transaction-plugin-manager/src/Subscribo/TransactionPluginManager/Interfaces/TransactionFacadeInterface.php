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
     * @return null|array
     */
    public function getAnswerFromQuestionary();

    /**
     * @return null|array
     */
    public function getAnswerFromWidget();

    /**
     * @return null|array
     */
    public function getAnswerFromClientRedirection();

    /**
     * @return bool;
     */
    public function isChargeTransaction();
}
