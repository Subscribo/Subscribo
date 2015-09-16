<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\DeliveryWindowType;
use Subscribo\Support\DateTimeUtils;

/**
 * Model DeliveryWindow
 *
 * Model class for being changed and used in the application
 */
class DeliveryWindow extends \Subscribo\ModelCore\Bases\DeliveryWindow
{
    /**
     * @param Delivery $delivery
     * @param DeliveryWindowType $deliveryWindowType
     * @return DeliveryWindow
     */
    public static function generate(Delivery $delivery, DeliveryWindowType $deliveryWindowType)
    {
        $instance = new static();
        $instance->delivery()->associate($delivery);
        $instance->deliveryWindowType()->associate($deliveryWindowType);
        $instance->start = DateTimeUtils::makeDateTime($deliveryWindowType->start.$delivery->start->format(' Y-m-d'));
        $instance->end = DateTimeUtils::makeDateTime($deliveryWindowType->end.$instance->start->format(' Y-m-d'));
        $instance->save();

        return $instance;
    }

    /**
     * @param Delivery|int $delivery
     * @param DeliveryWindowType|int $deliveryWindowType
     * @return mixed
     */
    public static function findByDeliveryAndDeliveryWindowType($delivery, $deliveryWindowType)
    {
        return static::byDelivery($delivery)->byDeliveryWindowType($deliveryWindowType)->first();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Delivery|int $delivery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDelivery($query, $delivery)
    {
        $deliveryId = ($delivery instanceof Delivery) ? $delivery->id : $delivery;
        $query->where('delivery_id', $deliveryId);

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param DeliveryWindowType|int $deliveryWindowType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDeliveryWindowType($query, $deliveryWindowType)
    {
        $deliveryWindowTypeId = ($deliveryWindowType instanceof DeliveryWindowType)
            ? $deliveryWindowType->id : $deliveryWindowType;
        $query->where('delivery_window_type_id', $deliveryWindowTypeId);

        return $query;
    }
}
