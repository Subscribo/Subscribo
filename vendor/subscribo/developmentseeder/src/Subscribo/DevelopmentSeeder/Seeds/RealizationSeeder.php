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
        $services = Service::all();
        foreach ($services as $service) {
            $deliveries = Delivery::query()->where(['service_id' => $service->id])->get();
            $products = Product::query()->where(['service_id' => $service->id])->get();

            foreach ($deliveries as $delivery) {
                foreach ($products as $product) {
                    $realization = Realization::generate($product, $delivery->id);
                    $this->display($realization);
                }
            }
        }
    }

    protected function display(Realization $realization)
    {
        if (empty($this->command)) {
            return null;
        }
        $this->command->getOutput()->writeln(sprintf('Realization of product %s for delivery %s', $realization->product->name, $realization->deliveryId));
    }
}
