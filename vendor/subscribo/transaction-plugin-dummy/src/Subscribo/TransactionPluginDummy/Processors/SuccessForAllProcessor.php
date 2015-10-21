<?php

namespace Subscribo\TransactionPluginDummy\Processors;

use Subscribo\TransactionPluginManager\Bases\TransactionProcessorBase;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;

/**
 * Class SuccessForAllProcessor
 * @package Subscribo\TransactionPluginDummy
 */
class SuccessForAllProcessor extends TransactionProcessorBase
{
    /**
     * @return TransactionProcessingResultBase
     */
    protected function doProcess()
    {
        $transaction = $this->transaction->getTransactionModelInstance();
        $transaction->changeStage(
            $transaction::STAGE_FINISHED,
            $transaction::STATUS_ACCEPTED,
            ['sent', 'receive', 'finalize']
        );
        $result = new TransactionProcessingResultBase(
            $this->transaction,
            TransactionProcessingResultBase::STATUS_SUCCESS
        );

        return $result;
    }
}
