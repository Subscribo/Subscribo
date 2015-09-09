<?php

namespace Subscribo\ApiServerJob\Jobs\Automatic;

use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
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
        $sendMessage = true; //todo implement callback logic
        $processingResult = $processingManager->charge($this->transaction, $sendMessage, false);
        //todo send Sales Order confirmation, if needed

        $logger->info("Processing transaction with hash '".$this->transaction->hash."' finished");
    }
}
