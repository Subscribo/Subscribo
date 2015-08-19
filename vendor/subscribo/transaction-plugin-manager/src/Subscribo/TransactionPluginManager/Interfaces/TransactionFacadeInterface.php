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
     * @param null|string $key
     * @return null|array|mixed
     */
    public function getDataToRemember($key = null);

    /**
     * @param mixed $value
     * @param null|string $key
     * @return $this
     */
    public function setDataToRemember($value, $key = null);

    /**
     * @return bool;
     */
    public function isChargeTransaction();
}
