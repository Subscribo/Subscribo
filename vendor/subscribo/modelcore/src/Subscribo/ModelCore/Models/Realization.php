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

    public static function generate(Product $product, $deliveryId = null, $identifier = true, $names = true, $descriptions = true)
    {
        $instance = static::make($product, $deliveryId, $identifier);
        $instance->save();

        return $instance;
    }

    public static function make(Product $product, $deliveryId = null, $identifier = true)
    {
        $instance = static::firstOrNew([
            'product_id' => $product->id,
            'delivery_id' => $deliveryId,
        ]);
        if (true === $identifier) {
            $identifier = 'REALIZATION_'.($deliveryId ?: 'FOR').'_'.$product->identifier;
            $instance->comment = 'Identifier generated automatically';
        }
        $instance->identifier = $identifier;
        $instance->serviceId = $product->serviceId;

        return $instance;
    }


}
