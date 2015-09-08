<?php

namespace Subscribo\ApiServerJob\Jobs\Regular;

use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\Realization;
use Subscribo\Support\DateTimeUtils;
use Psr\Log\LoggerInterface;

/**
 * Class MaintainDeliveries
 *
 * @package Subscribo\ApiServerJob
 */
class MaintainDeliveries extends AbstractJob
{
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
        $log->info("Maintaining Deliveries for service '".$this->service->identifier."' started");

        $addedDeliveries = Delivery::autoAdd($this->service);

        foreach ($addedDeliveries as $delivery) {
            $log->notice('Added delivery starting from '.DateTimeUtils::exportDateTime($delivery->start));
        }
        $log->info("Deliveries added: ". count($addedDeliveries));

        $updated = Delivery::autoAvailable($this->service);

        $log->notice(
            "Deliveries in which availability for ordering was enabled: ".count($updated['enabled'])
            .", disabled: ".count($updated['disabled'])
            .", stayed enabled: ".count($updated['stayedEnabled'])
            .", stayed disabled: ".count($updated['stayedDisabled'])
        );
        $log->info("Maintaining Deliveries for service '".$this->service->identifier."' finished");
        $log->info("Maintaining Product Realizations for service '".$this->service->identifier."' started");

        $products = $this->service->products;

        $restoredRealizationsCount = Realization::restoreInBoundsRealizations($products);

        $log->notice("Realizations restored: ".$restoredRealizationsCount);

        $softDeletedRealizationsCount = Realization::softDeleteOutOfBoundsRealizations($products);

        $log->notice("Realizations soft deleted: ".$softDeletedRealizationsCount);

        $suppliedRealizations = Realization::supplyRealizations($products, $this->service->deliveries);

        foreach ($suppliedRealizations as $realization) {
                $log->notice(
                    "Supplied realization '".$realization->identifier
                    ."' for product '".$realization->product->identifier
                    ."' for delivery starting from ".DateTimeUtils::exportDateTime($realization->delivery->start)
                );
        }
        $log->info("Realizations supplied: ".count($suppliedRealizations));
        $log->info("Maintaining Product Realizations for service '".$this->service->identifier."' finished");
    }
}

