<?php

namespace Subscribo\ModelCore\Models;

use InvalidArgumentException;
use DateTime;
use DateInterval;
use Subscribo\ModelCore\Traits\FilterableByServiceTrait;
use Subscribo\ModelCore\Models\Service;
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
     * @param int|Service $service
     * @param DateTime|string $start
     * @param bool $isAvailableForOrdering
     * @return Delivery
     */
    public static function generate($service, $start = 'today', $isAvailableForOrdering = false)
    {
        $instance = static::make($service, $start, $isAvailableForOrdering);
        $instance->save();

        return $instance;
    }

    /**
     * @param int|Service $service
     * @param DateTime|string $start
     * @param bool $isAvailableForOrdering
     * @return Delivery
     * @throws \InvalidArgumentException
     */
    public static function make($service, $start = 'today', $isAvailableForOrdering = false)
    {
        if (empty($service)) {
            throw new InvalidArgumentException('Service should not be empty');
        }
        $instance = new static();
        $instance->service()->associate($service);
        $instance->start = ($start instanceof DateTime) ? $start : new DateTime($start);
        $instance->isAvailableForOrdering = $isAvailableForOrdering;

        return $instance;
    }

    /**
     * @param int|Service $service
     * @return Delivery[]
     */
    public static function autoAdd($service)
    {
        $added = [];
        $lastDelivery = static::byService($service)->lastDefined()->first();
        if (empty($lastDelivery)) {
            $lastDelivery = static::generate($service);
            $added[] = $lastDelivery;
        }
        if (empty($service->deliveryAutoAddLimit) or empty($service->deliveryPeriod)) {

            return $added;
        }
        $period = DateInterval::createFromDateString($service->deliveryPeriod);
        $limit = DateTimeUtils::makeDate($service->deliveryAutoAddLimit, false);
        $lastStart = DateTimeUtils::makeDate($lastDelivery->start);
        $nextStart = clone $lastStart;
        $nextStart->add($period);
        while ($nextStart <= $limit) {
            $added[] = static::generate($service, $nextStart);
            $nextStart = clone $nextStart;
            $nextStart->add($period);
        }

        return $added;
    }

    /**
     * @param int|Service $service
     * @return array
     */
    public static function autoAvailable($service)
    {
        $enabled = [];
        $disabled = [];
        $stayedEnabled = [];
        $stayedDisabled = [];
        $start = DateTimeUtils::makeDate($service->deliveryAutoAvailableStart);
        $end = DateTimeUtils::makeDate($service->deliveryAutoAvailableEnd);
        $deliveries = static::getAllByService($service);
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
     * @param int $limit
     * @return Delivery[]
     */
    public static function getAvailableForOrderingByService($service, $limit)
    {
        return static::byService($service, false)->availableForOrdering($limit)->get();
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
        $from = ($when instanceof DateTime) ? $when  : new DateTime($when);
        $query->where('start', '>=', $from);

        return $query;
    }
}
