<?php

namespace Subscribo\ApiServerJob\Jobs\Regular;

use Subscribo\ApiServerJob\Jobs\Automatic\ProcessChargeTransaction;
use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\ModelCore\Models\Subscription;
use Subscribo\ModelCore\Models\Delivery;
use Psr\Log\LoggerInterface;
use Subscribo\ModelCore\Models\Transaction;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class MaintainSubscription
 *
 * @package Subscribo\ApiServerJob
 */
class MaintainSubscription extends AbstractJob
{
    use DispatchesJobs;

    /** @var \Subscribo\ModelCore\Models\Subscription  */
    protected $subscription;

    /**
     * @param Subscription $subscription
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function handle(LoggerInterface $logger)
    {
        $service = $this->subscription->service;
        if (empty($service->subscriptionAddSalesOrderStart) or empty ($service->subscriptionAddSalesOrderEnd)) {
            $logger->warning("Skipping subscription ID: ".$this->subscription->id
                ." as add sales order dates not defined for service");

            return;
        }
        $deliveries = Delivery::getAvailableForSubscriptionAddSalesOrderByService($service);
        if ( ! count($deliveries)) {
            $logger->info("Skipping subscription ID: ".$this->subscription->id
                ." as no deliveries are available at the moment for adding Sales Orders based on subscription");

            return;
        }
        $logger->info("Handling subscription ID: ".$this->subscription->id." start");
        foreach ($deliveries as $delivery) {
            $found = SalesOrder::findBySubscriptionAndDelivery($this->subscription, $delivery);
            if ($found) {
                $logger->info("Delivery starting from: '".$delivery->start."' skipped as existing SalesOrder found");

                continue;
            }
            if ( ! $this->subscription->dateIsWithinBoundaries($delivery->start)) {
                $logger->info("Delivery starting from: '".$delivery->start
                        ."' skipped as is out of Subscription boundaries");

                continue;
            }
            if ($this->subscription->dateIsWithinSubscriptionVeto($delivery->start)) {
                $logger->info("Delivery starting from: '".$delivery->start
                        ."' skipped as is within a Subscription Veto");

                continue;
            }
            if ($this->subscription->dateIsFilteredOut($delivery->start)) {
                $logger->info("Delivery starting from: '".$delivery->start
                        ."' skipped as it was filtered out by Subscription Filter");

                continue;
            }
            $TGConfig = $this->subscription->account->customer->defaultTransactionGatewayConfiguration;
            if (empty($TGConfig)) {
                $logger->error("SalesOrder for delivery starting from: '".$delivery->start
                        ."' not generated as no defaultTransactionGatewayConfiguration defined for customer");

                continue;
            }
            $salesOrder = SalesOrder::generateFromSubscriptionForDelivery($this->subscription, $delivery);
            $logger->notice("Sales order generated for delivery starting from: '".$delivery->start
                        ."'. Total gross amount: ".$salesOrder->grossSum." ".$salesOrder->currency->symbol);
            $transaction = Transaction::generateFromSalesOrder($salesOrder, $TGConfig, Transaction::ORIGIN_SYSTEM);
            $processChargeTransactionJob = new ProcessChargeTransaction($transaction);
            $this->dispatch($processChargeTransactionJob);
            $logger->notice("Processing charge transaction job for transaction hash: '"
                        .$transaction->hash."' dispatched");
        }

        $logger->info("Handling subscription ID: ".$this->subscription->id." finished");
    }
}
