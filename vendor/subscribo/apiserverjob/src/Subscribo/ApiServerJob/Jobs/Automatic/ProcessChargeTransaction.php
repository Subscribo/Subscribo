<?php

namespace Subscribo\ApiServerJob\Jobs\Automatic;

use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ApiServerJob\Jobs\Triggered\SalesOrder\SendConfirmationMessage as SendSalesOrderConfirmationMessage;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Managers\TransactionProcessingManager;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Psr\Log\LoggerInterface;

/**
 * Class ProcessChargeTransaction
 *
 * @package Subscribo\ApiServerJob
 */
class ProcessChargeTransaction extends AbstractJob
{
    use DispatchesJobs;

    /** @var \Subscribo\ModelCore\Models\Transaction  */
    protected $transaction;

    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param LoggerInterface $logger
     * @param PluginResourceManagerInterface $resourceManager
     */
    public function handle(LoggerInterface $logger, PluginResourceManagerInterface $resourceManager)
    {
        $logger->info("Processing transaction with hash '".$this->transaction->hash."' started");
        $driverName = $this->transaction->transactionGatewayConfiguration->transactionGateway->driver;
        $processingManager = new TransactionProcessingManager($resourceManager, $driverName);
        $sendMessage = function ($result, TransactionProcessingResultInterface $processingResult) {
            if (TransactionProcessingResultInterface::STATUS_SKIPPED === $processingResult->getStatus()) {

                return false;
            }
            switch(strval($processingResult->getTransactionFacadeObject()->getTransactionModelInstance()->result)) {
                case Transaction::RESULT_SUCCESS:
                case Transaction::RESULT_FAILURE:
                case Transaction::RESULT_CANCELLED:
                case Transaction::RESULT_WAITING:

                    return true;
                default:

                    return false;
            }
        };
        $processResult = $processingManager->charge($this->transaction, $sendMessage, false);
        $salesOrder = $this->transaction->salesOrder;
        $status = empty($processResult['result']['status']) ? '' : $processResult['result']['status'];
        if ((TransactionProcessingResultInterface::STATUS_SUCCESS === $status)
            and $salesOrder
            and (SalesOrder::STATUS_ORDERING === $salesOrder->status)) {
            $messageSendingJob = new SendSalesOrderConfirmationMessage($salesOrder);
            $this->dispatch($messageSendingJob);
            $logger->info("Sending message job for sales order with hash '".$salesOrder->hash."' dispatched");
            $salesOrder->status = SalesOrder::STATUS_ORDERED;
            $salesOrder->save();
        } else {
            $reasonAdd = empty($processResult['result']['reason'])
                ? '' : (' reason: '.$processResult['result']['reason']);
            $logger->warning("Processing transaction with hash '"
                .$this->transaction->hash."' status: ".$status.$reasonAdd);
        }
        $logger->info("Processing transaction with hash '".$this->transaction->hash."' finished");
    }
}
