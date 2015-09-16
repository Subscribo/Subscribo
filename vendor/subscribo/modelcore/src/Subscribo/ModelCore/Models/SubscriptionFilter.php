<?php

namespace Subscribo\ModelCore\Models;

use UnexpectedValueException;
use Subscribo\ModelCore\Models\Subscription;
use Subscribo\Support\DateTimeUtils;

/**
 * Model SubscriptionFilter
 *
 * Model class for being changed and used in the application
 */
class SubscriptionFilter extends \Subscribo\ModelCore\Bases\SubscriptionFilter
{
    /**
     * @param Subscription|int $subscription
     * @param string $type
     * @return SubscriptionFilter
     */
    public static function generate($subscription, $type)
    {
        $instance = static::make($subscription, $type);
        $instance->save();

        return $instance;
    }

    /**
     * @param Subscription|int $subscription
     * @param string $type
     * @return SubscriptionFilter
     */
    public static function make($subscription, $type)
    {
        $instance = new static();
        $instance->subscription()->associate($subscription);
        $instance->type = $type;

        return $instance;
    }

    /**
     * @param Subscription|int $subscription
     * @param string $type
     * @return SubscriptionFilter
     */
    public static function enableFilterForSubscription($subscription, $type)
    {
        $found = static::withTrashed()->bySubscription($subscription)->byType($type)->first();
        if (empty($found)) {

            return static::generate($subscription, $type);
        }
        /** @var $found self */
        if ($found->trashed()) {
            $found->restore();
        }

        return $found;
    }

    /**
     * @param Subscription|int $subscription
     * @param string $type
     * @return bool|null
     */
    public static function disableFilterForSubscription($subscription, $type)
    {
        $found = static::bySubscription($subscription)->byType($type)->first();
        if (empty($found)) {

            return null;
        }
        /** @var $found self */
        $found->delete();

        return true;
    }

    /**
     * @param string|\DateTime $when
     * @return bool
     * @throws \UnexpectedValueException
     */
    public function dateIsFilteredOut($when)
    {
        $date = DateTimeUtils::makeDate($when);
        switch (strval($this->type)) {
            case static::TYPE_MONDAY:

                return ($date->format('N') === '1');
            case static::TYPE_TUESDAY:

                return ($date->format('N') === '2');
            case static::TYPE_WEDNESDAY:

                return ($date->format('N') === '3');
            case static::TYPE_THURSDAY:

                return ($date->format('N') === '4');
            case static::TYPE_FRIDAY:

                return ($date->format('N') === '5');
            case static::TYPE_SATURDAY:

                return ($date->format('N') === '6');
            case static::TYPE_SUNDAY:

                return ($date->format('N') === '7');
            case static::TYPE_EVEN_WEEK:

                return ((intval($date->format('W')) % 2) === 0);
            case static::TYPE_ODD_WEEK:

                return ((intval($date->format('W')) % 2) === 1);
            default:
                throw new UnexpectedValueException('Handling of provided subscription filter type not implemented');
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Subscription|int $subscription
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySubscription($query, $subscription)
    {
        $subscriptionId = ($subscription instanceof Subscription) ? $subscription->id : $subscription;
        $query->where('subscription_id', $subscriptionId);

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        $query->where('type', $type);

        return $query;
    }
}
