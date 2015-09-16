<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Service;
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
        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();
        $this->addDeliveryAndWindows($frontendService, 'Tuesday');
        $mainService = Service::query()->where(['identifier' => 'MAIN'])->first();
        $this->addDeliveryAndWindows($mainService, 'Monday');
    }

    /**
     * @param Service $service
     * @param string $seedStart
     */
    protected function addDeliveryAndWindows(Service $service, $seedStart)
    {
        $usualDeliveryWindowTypes = DeliveryWindowType::getAllUsualByService($service);
        $addedDeliveries = Delivery::autoAdd($service, $seedStart);
        foreach ($addedDeliveries as $delivery) {
            $deliveryWindows = [];
            foreach ($usualDeliveryWindowTypes as $deliveryWindowType) {
                $deliveryWindows[] = DeliveryWindow::generate($delivery, $deliveryWindowType);
            }
        }
    }
}
