<?php

namespace Subscribo\ModelCore\Models;

use DateTime;
use DateInterval;
use Subscribo\ModelCore\Traits\FilterableByServiceTrait;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\DeliveryWindow;
use Subscribo\ModelCore\Models\DeliveryPlan;
use Subscribo\Support\DateTimeUtils;

/**
 * Model Delivery
 *
 * Model class for being changed and used in the application
 *
 * @method \Illuminate\Database\Eloquent\Builder availableForOrdering() public static availableForOrdering(int|null $limit) scope limiting the result to those deliveries, which are available for ordering, ordering by start and optionally limiting by $limit count
 */
class Delivery extends \Subscribo\ModelCore\Bases\Delivery
{
    use FilterableByServiceTrait;

    /**
     * @param DeliveryPlan $deliveryPlan
     * @param DateTime|string $start
     * @param bool $isAvailableForOrdering
     * @return Delivery
     */
    public static function generate(DeliveryPlan $deliveryPlan, $start = 'today', $isAvailableForOrdering = false)
    {
        $instance = static::make($deliveryPlan, $start, $isAvailableForOrdering);
        $instance->save();

        return $instance;
    }

    /**
     * @param DeliveryPlan $deliveryPlan
     * @param DateTime|string $start
     * @param bool $isAvailableForOrdering
     * @return Delivery
     */
    public static function make(DeliveryPlan $deliveryPlan, $start = 'today', $isAvailableForOrdering = false)
    {
        $instance = new static();
        $instance->deliveryPlan()->associate($deliveryPlan);
        $instance->service()->associate($deliveryPlan->serviceId);

        $instance->start = DateTimeUtils::makeDateTime($start);
        $instance->isAvailableForOrdering = $isAvailableForOrdering;

        return $instance;
    }

    /**
     * @param DeliveryPlan $deliveryPlan
     * @return Delivery[]
     */
    public static function autoAdd(DeliveryPlan $deliveryPlan)
    {
        $added = [];
        $lastDelivery = static::byDeliveryPlan($deliveryPlan)->lastDefined()->first();
        if (empty($lastDelivery)) {
            if (empty($deliveryPlan->seedStart)) {

                return [];
            }
            $lastDelivery = static::generate($deliveryPlan, $deliveryPlan->seedStart);
            $added[] = $lastDelivery;
        }
        if (empty($deliveryPlan->deliveryAutoAddLimit) or empty($deliveryPlan->deliveryPeriod)) {

            return $added;
        }
        $period = DateInterval::createFromDateString($deliveryPlan->deliveryPeriod);
        $limit = DateTimeUtils::makeDate($deliveryPlan->deliveryAutoAddLimit, false);
        $lastStart = DateTimeUtils::makeDate($lastDelivery->start);
        $nextStart = clone $lastStart;
        $nextStart->add($period);
        while ($nextStart <= $limit) {
            $added[] = static::generate($deliveryPlan, $nextStart);
            $nextStart = clone $nextStart;
            $nextStart->add($period);
        }

        return $added;
    }

    /**
     * @param DeliveryPlan $deliveryPlan
     * @return array
     */
    public static function autoAvailable(DeliveryPlan $deliveryPlan)
    {
        $enabled = [];
        $disabled = [];
        $stayedEnabled = [];
        $stayedDisabled = [];
        $start = DateTimeUtils::makeDate($deliveryPlan->deliveryAutoAvailableStart);
        $end = DateTimeUtils::makeDate($deliveryPlan->deliveryAutoAvailableEnd);
        $deliveries = static::byDeliveryPlan($deliveryPlan)->get();
        /** @var Delivery $delivery */
        foreach ($deliveries as $delivery) {
            $deliveryStart = DateTimeUtils::makeDate($delivery->start);
            if ($delivery->isAvailableForOrdering) {
                if ($start and ($deliveryStart < $start)) {
                    $delivery->isAvailableForOrdering = false;   //Disabling too old deliveries
                    $delivery->save();
                    $disabled[] = $delivery;
                } else {
                    $stayedEnabled[] = $delivery;
                }
            } else {
                if ($start and $end and ($start <= $deliveryStart) and ($deliveryStart <= $end)) {
                    $delivery->isAvailableForOrdering = true;   //Enabling deliveries within time constraints
                    $delivery->save();
                    $enabled[] = $delivery;
                } else {
                    $stayedDisabled[] = $delivery;
                }
            }
        }

        return [
            'enabled' => $enabled,
            'disabled' => $disabled,
            'stayedEnabled' => $stayedEnabled,
            'stayedDisabled' => $stayedDisabled,
        ];
    }

    /**
     * @param int|Service $service
     * @param int|null $limit
     * @return Delivery[]
     */
    public static function getAvailableForOrderingByService($service, $limit)
    {
        return static::byService($service, false)->availableForOrdering($limit)->get();
    }

    /**
     * @param int|DeliveryPlan $deliveryPlan
     * @param int|null $limit
     * @return Delivery[]
     */
    public static function getAvailableForOrderingByDeliveryPlan($deliveryPlan, $limit)
    {
        return static::byDeliveryPlan($deliveryPlan)->availableForOrdering($limit)->get();
    }


    /**
     * Returns those deliveries, which are connected to particular Service
     * and their start date is within boundaries, set by that particular service,
     * for automatic adding Sales Orders based on a running Subscription
     * @param Service $service
     * @return Delivery[]
     */
    public static function getAvailableForSubscriptionAddSalesOrderByService(Service $service)
    {
        $start = DateTimeUtils::makeDate($service->subscriptionAddSalesOrderStart);
        $end = DateTimeUtils::makeDate($service->subscriptionAddSalesOrderEnd);
        if (empty($start) or empty($end)) {

            return [];
        }

        return static::byService($service, false)->startFrom($start)->startTo($end)->get();
    }

    public function getDeliveryWindowByType($deliveryWindowType)
    {
        return DeliveryWindow::findByDeliveryAndDeliveryWindowType($this, $deliveryWindowType);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param DeliveryPlan|int $deliveryPlan
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDeliveryPlan($query, $deliveryPlan)
    {
        $deliveryPlanId = ($deliveryPlan instanceof DeliveryPlan) ? $deliveryPlan->id : $deliveryPlan;
        $query->where('delivery_plan_id', $deliveryPlanId);

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param null|int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailableForOrdering($query, $limit = null)
    {
        $query->where('is_available_for_ordering', true);
        if ($limit) {
            $query->take($limit);
        }
        $query->orderBy('start');

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastDefined($query, $limit = 1)
    {
        if ($limit) {
            $query->take($limit);
        }
        $query->orderBy('start', 'desc');

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param DateTime|string $when
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeStartFrom($query, $when = 'today')
    {
        $dateFrom = ($when instanceof DateTime) ? $when  : new DateTime($when);
        $query->where('start', '>=', $dateFrom);

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param DateTime|string $when
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeStartTo($query, $when = 'today')
    {
        $dateTo = ($when instanceof DateTime) ? $when  : new DateTime($when);
        $query->where('start', '<=', $dateTo);

        return $query;
    }
}
