<?php

namespace Subscribo\ApiServerCommon\Jobs\Regular;

use Subscribo\ApiServerCommon\Jobs\AbstractJob;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\Realization;
use Subscribo\Support\DateTimeUtils;
use Psr\Log\LoggerInterface;

/**
 * Class MaintainDeliveries
 *
 * @package Subscribo\ApiServerCommon
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
        $log->notice("Maintaining Deliveries for service '".$this->service->identifier."' started");

        $addedDeliveries = Delivery::autoAdd($this->service);

        foreach ($addedDeliveries as $delivery) {
            $log->notice('Added delivery starting from '.DateTimeUtils::exportDateTime($delivery->start));
        }
        $log->notice("Deliveries added: ". count($addedDeliveries));

        $updated = Delivery::autoAvailable($this->service);

        $log->notice(
            "Deliveries in which availability for ordering was enabled: ".count($updated['enabled'])
            .", disabled: ".count($updated['disabled'])
            .", stayed enabled: ".count($updated['stayedEnabled'])
            .", stayed disabled: ".count($updated['stayedDisabled'])
        );
        $log->notice("Maintaining Deliveries for service '".$this->service->identifier."' finished");
        $log->notice("Maintaining Product Realizations for service '".$this->service->identifier."' started");

        $suppliedRealizations = Realization::supplyRealizations($this->service->products, $this->service->deliveries);

        foreach ($suppliedRealizations as $realization) {
                $log->notice(
                    "Supplied realization '".$realization->identifier
                    ."' for product '".$realization->product->identifier
                    ."' for delivery starting from ".DateTimeUtils::exportDateTime($realization->delivery->start)
                );
        }
        $log->notice("Realizations supplied: ".count($suppliedRealizations));
        $log->notice("Maintaining Product Realizations for service '".$this->service->identifier."' finished");
    }
}

