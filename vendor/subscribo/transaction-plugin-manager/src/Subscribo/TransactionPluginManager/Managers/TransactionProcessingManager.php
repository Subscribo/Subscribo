<?php

namespace Subscribo\TransactionPluginManager\Managers;

use RuntimeException;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginChargeDriverInterface;
use Subscribo\TransactionPluginManager\Facades\TransactionFacade;
use Subscribo\ModelCore\Models\Transaction;

/**
 * Class TransactionProcessingManager
 *
 * @package Subscribo\TransactionPluginManager
 */
class TransactionProcessingManager
{
    /** @var PluginResourceManagerInterface  */
    protected $transactionPluginManager;

    /** @var \Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface  */
    protected $driver;

    /**
     * @param PluginResourceManagerInterface $transactionPluginManager
     * @param \Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface|string $driver
     */
    public function __construct(PluginResourceManagerInterface $transactionPluginManager, $driver)
    {
        $this->transactionPluginManager = $transactionPluginManager;
        $this->driver = $transactionPluginManager->getDriver($driver);
    }

    /**
     * @param Transaction $transaction
     * @param bool|callable $sendMessage Whether to send email
     * @param bool|callable $throwExceptions Whether to throw exceptions
     * @param bool|callable $shouldLog Whether to log log messages
     * @return array
     * @throws \Exception
     * @throws \Subscribo\RestCommon\Exceptions\ServerRequestHttpException
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     * @throws \Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     * @throws \Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException
     * @throws \Subscribo\Exception\Exceptions\ServerErrorHttpException
     * @throws \Subscribo\Exception\Exceptions\ClientErrorHttpException
     */
    public function charge(Transaction $transaction, $sendMessage = true, $throwExceptions = true, $shouldLog = true)
    {
        return $this->transactionPluginManager->finalizeTransactionProcessingResult(
            $this->processChargeTransaction($transaction),
            $sendMessage,
            $throwExceptions,
            $shouldLog
        );
    }

    /**
     * @param Transaction $transaction
     * @return \Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface
     * @throws \RuntimeException
     */
    public function processChargeTransaction(Transaction $transaction)
    {
        if ( ! ($this->driver instanceof TransactionPluginChargeDriverInterface)) {

            throw new RuntimeException('Provided driver is not able to process charge transactions');
        }
        $transactionFacade = new TransactionFacade($transaction);
        if ( ! $transactionFacade->isChargeTransaction()) {

            throw new RuntimeException('Provided transaction is not charge');
        }
        switch (strval($transaction->result)) {
            case Transaction::RESULT_SUCCESS:
            case Transaction::RESULT_CANCELLED:
            case Transaction::RESULT_FAILURE:

                return TransactionProcessingResultBase::makeSkippedResult(
                    $transactionFacade,
                    TransactionProcessingResultBase::SKIPPED_PROCESSED
                );
        }

        return $this->driver->makeProcessor($transactionFacade)->process();
    }
}
