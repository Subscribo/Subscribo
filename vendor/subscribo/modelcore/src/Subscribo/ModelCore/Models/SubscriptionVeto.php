<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\Support\DateTimeUtils;

/**
 * Model SubscriptionVeto
 *
 * Model class for being changed and used in the application
 */
class SubscriptionVeto extends \Subscribo\ModelCore\Bases\SubscriptionVeto
{
    /**
     * @param Subscription|int $subscription
     * @param \DateTime|string $when
     * @return SubscriptionVeto[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function findVetosForSubscriptionContainingDate($subscription, $when)
    {
        return static::bySubscription($subscription)->containingDate($when)->get();
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
     * @param \DateTime|string $when
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContainingDate($query, $when)
    {
        $date = DateTimeUtils::makeDate($when);
        $query->where(function ($q) use ($date) {
            /** @var \Illuminate\Database\Query\Builder $q */
            $q->where('start', '<=', $date);
            $q->orWhereNull('start');
        });
        $query->where(function ($q) use ($date) {
            /** @var \Illuminate\Database\Query\Builder $q */
            $q->where('end', '>=', $date);
            $q->orWhereNull('end');
        });

        return $query;
    }
}
