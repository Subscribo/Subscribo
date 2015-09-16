<?php

namespace Subscribo\ApiServerJob\Jobs\Regular;

use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ApiServerJob\Jobs\Regular\MaintainDeliveries;
use Subscribo\ApiServerJob\Jobs\Regular\MaintainSubscription;
use Subscribo\ModelCore\Models\Service;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Psr\Log\LoggerInterface;

/**
 * Class ServiceMaintenance
 *
 * @package Subscribo\ApiServerJob
 */
class ServiceMaintenance extends AbstractJob
{
    use DispatchesJobs;

    /** @var \Subscribo\ModelCore\Models\Service  */
    protected $service;

    /**
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * @param LoggerInterface $log
     */
    public function handle(LoggerInterface $log)
    {
        $log->info("Maintenance of service '".$this->service->identifier."' (dispatching regular jobs) started.");

        $maintainDeliveriesJob = new MaintainDeliveries($this->service);
        $this->dispatch($maintainDeliveriesJob);

        $log->info("Deliveries maintenance for service '".$this->service->identifier."' dispatched.");
        foreach ($this->service->subscriptions as $subscription) {
            $maintainSubscriptionJob = new MaintainSubscription($subscription);
            $this->dispatch($maintainSubscriptionJob);
            $log->info("Subscription ID '".$subscription->id."' maintenance dispatched.");
        }
        $log->info("Maintenance of service '".$this->service->identifier."' (dispatching regular jobs) finished.");
    }
}
