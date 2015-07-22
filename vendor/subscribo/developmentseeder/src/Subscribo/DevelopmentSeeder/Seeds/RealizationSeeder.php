<?php

namespace Subscribo\DevelopmentSeeder\Seeds;

use Illuminate\Database\Seeder;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\Realization;

use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\Product;

/**
 * Class RealizationSeeder
 *
 * @package Subscribo\DevelopmentSeeder
 */
class RealizationSeeder extends Seeder
{
    public function run()
    {
        $frontendService = Service::query()->where(['identifier' => 'FRONTEND'])->first();
        $deliveries = Delivery::query()->where(['service_id' => $frontendService->id])->get();
        $products = Product::query()->where(['service_id' => $frontendService->id])->get();

        foreach ($deliveries as $delivery) {
            foreach ($products as $product) {
                $realization = Realization::generate($product, $delivery->id);
            }
        }
    }
}
