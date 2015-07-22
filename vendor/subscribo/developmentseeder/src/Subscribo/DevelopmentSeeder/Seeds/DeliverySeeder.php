<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\Service;

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

        $delivery1 = new Delivery();
        $delivery1->start = date('Y-m-d H:i:s');
        $delivery1->serviceId = $frontendService->id;
        $delivery1->save();
    }
}
