<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Product;
use Subscribo\ModelCore\Models\Delivery;

/**
 * Model Realization
 *
 * Model class for being changed and used in the application
 */
class Realization extends \Subscribo\ModelCore\Bases\Realization
{
    /**
     * @param int $serviceId
     * @param int $productId
     * @param int|null $deliveryId
     * @param bool $autoFallback
     * @return null|\Subscribo\ModelCore\Models\Realization
     */
    public static function findByAttributes($serviceId, $productId, $deliveryId = null, $autoFallback = true)
    {
        $mainQuery = static::query();
        $mainQuery->where('service_id', $serviceId);
        $mainQuery->where('product_id', $productId);
        if ($deliveryId) {
            $mainQuery->where('delivery_id', $deliveryId);
        } else {
            $mainQuery->whereNull('delivery_id');
        }
        $instance = $mainQuery->first();
        if ($instance or empty($deliveryId)) {

            return $instance;
        }
        if ($autoFallback) {
            return static::findByAttributes($serviceId, $productId, null, false);
        }

        return null;
    }

    /**
     * @param Product[] $products
     * @param Delivery[] $deliveries
     * @return Realization[]
     */
    public static function supplyRealizations($products, $deliveries)
    {
        $supplied = [];
        foreach ($products as $product) {
            $serviceId = $product->serviceId;
            foreach ($deliveries as $delivery) {
                $found = static::findByAttributes($serviceId, $product->id, $delivery->id, false);
                if ($found) {
                    continue;
                }
                $realization = static::generate($product, $delivery);
                $supplied[] = $realization;
            }
        }
        return $supplied;
    }

    /**
     * @param Product $product
     * @param Delivery|int|null $delivery
     * @param string|bool $identifier
     * @param bool $names
     * @param bool $descriptions
     * @return Realization
     */
    public static function generate(Product $product, $delivery = null, $identifier = true, $names = true, $descriptions = true)
    {
        $instance = static::make($product, $delivery, $identifier);
        $instance->save();

        return $instance;
    }

    /**
     * @param Product $product
     * @param null $delivery
     * @param string|bool $identifier
     * @return Realization
     */
    public static function make(Product $product, $delivery = null, $identifier = true)
    {
        $deliveryId = ($delivery instanceof Delivery) ? $delivery->id : $delivery;
        $instance = static::firstOrNew([
            'product_id' => $product->id,
            'delivery_id' => $deliveryId,
        ]);
        if (true === $identifier) {
            $identifier = 'REALIZATION_'.($deliveryId ?: 'FOR').'_'.$product->identifier;
            $instance->comment = 'Identifier generated automatically';
        }
        $instance->product()->associate($product);
        $instance->delivery()->associate($delivery);
        $instance->identifier = $identifier;
        $instance->serviceId = $product->serviceId;

        return $instance;
    }
}
