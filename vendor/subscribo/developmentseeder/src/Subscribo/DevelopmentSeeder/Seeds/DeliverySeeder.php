<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\DeliveryPlan;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\DeliveryWindow;
use Subscribo\ModelCore\Models\DeliveryWindowType;

/**
 * Class DeliverySeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class DeliverySeeder extends Seeder
{
    public function run()
    {
        /** @var Service $frontendService */
        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();
        foreach ($frontendService->deliveryPlans as $plan) {
            $this->addDeliveryAndWindows($plan);
        }
        $mainService = Service::query()->where(['identifier' => 'MAIN'])->first();
        foreach ($mainService->deliveryPlans as $plan) {
            $this->addDeliveryAndWindows($plan);
        }
    }

    /**
     * @param DeliveryPlan $deliveryPlan
     */
    protected function addDeliveryAndWindows(DeliveryPlan $deliveryPlan)
    {
        $usualDeliveryWindowTypes = DeliveryWindowType::getAllUsualByDeliveryPlan($deliveryPlan);
        $addedDeliveries = Delivery::autoAdd($deliveryPlan);
        foreach ($addedDeliveries as $delivery) {
            $deliveryWindows = [];
            foreach ($usualDeliveryWindowTypes as $deliveryWindowType) {
                $deliveryWindows[] = DeliveryWindow::generate($delivery, $deliveryWindowType);
            }
        }
    }
}
