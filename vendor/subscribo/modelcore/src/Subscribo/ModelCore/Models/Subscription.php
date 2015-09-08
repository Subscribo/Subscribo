<?php namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Discount;
use Subscribo\ModelCore\Models\DeliveryWindowType;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\SubscriptionVeto;
use Subscribo\ModelCore\Models\SubscriptionFilter;
use Subscribo\ModelCore\Models\ProductsInSubscription;
use Subscribo\Support\DateTimeUtils;


/**
 * Model Subscription
 *
 * Model class for being changed and used in the application
 */
class Subscription extends \Subscribo\ModelCore\Bases\Subscription
{
    /**
     * @param Account $account
     * @param Currency|int $currency
     * @param Country|int|null $country
     * @param Address|int|null $shippingAddress
     * @param Address|int|null|bool $billingAddress
     * @param array $subscriptionFilterTypes
     * @param \DateTime|string $start
     * @param DeliveryWindowType|int|null $deliveryWindowType
     * @param string $status
     * @return Subscription
     */
    public static function generateSubscription(
        Account $account,
        $currency,
        $country,
        $shippingAddress,
        $billingAddress = true,
        $subscriptionFilterTypes = [],
        $start = 'today',
        $deliveryWindowType = null,
        $status = self::STATUS_ACTIVE
    ) {
        if (true === $billingAddress) {
            $billingAddress = $shippingAddress;
        }
        $startDate = DateTimeUtils::makeDate($start);

        $subscription = new Subscription();
        $subscription->serviceId = $account->serviceId;
        $subscription->account()->associate($account);
        $subscription->currency()->associate($currency);
        $subscription->country()->associate($country);
        $subscription->shippingAddress()->associate($shippingAddress);
        $subscription->billingAddress()->associate($billingAddress);
        $subscription->status = $status;
        $subscription->deliveryWindowType()->associate($deliveryWindowType);
        $subscription->start = $startDate;
        $subscription->save();
        foreach ($subscriptionFilterTypes as $filterType) {
            $subscription->enableFilter($filterType);
        }

        return $subscription;
    }

    /**
     * @param Service|int $service
     * @param string $fromOrSooner
     * @return \Illuminate\Database\Eloquent\Collection|Subscription[]
     */
    public static function findActiveSubscriptionsForService($service, $fromOrSooner = 'today')
    {
        $serviceId = ($service instanceof Service) ? $service->id : $service;
        $query = static::query();
        $query->where('service_id', $serviceId);
        $query->where('status', static::STATUS_ACTIVE);
        $startBoundary = DateTimeUtils::makeDate($fromOrSooner);
        if ($startBoundary) {
            $query->where('start', "<=", $startBoundary);
        }

        return $query->get();
    }

    /**
     * @param Service $service
     * @return array
     */
    public static function getAvailableSubscriptionPeriods(Service $service)
    {
        return $service->getSubscriptionPeriods();
    }

    /**
     * @param $when
     * @return bool|null
     */
    public function dateIsWithinBoundaries($when)
    {
        $date = DateTimeUtils::makeDate($when);
        if (empty($date)) {

            return null;
        }
        if ($this->start and ($this->start > $date)) {

            return false;
        }
        if ($this->end and ($this->end < $date)) {

            return false;
        }

        return true;
    }

    /**
     * @param \DateTime|string $when
     * @return bool
     */
    public function dateIsWithinSubscriptionVeto($when)
    {
        $vetos = $this->getSubscriptionVetosForDate($when);
        if (empty($vetos)) {

            return false;
        }

        return (bool) count($vetos);
    }

    /**
     * @param \DateTime|string $when
     * @return bool
     */
    public function dateIsFilteredOut($when)
    {
        $filters = $this->subscriptionFilters;
        foreach ($filters as $filter) {
            if ($filter->dateIsFilteredOut($when)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $filterType
     * @return $this
     */
    public function enableFilter($filterType)
    {
        SubscriptionFilter::enableFilterForSubscription($this, $filterType);

        return $this;
    }

    /**
     * @param string $filterType
     * @return $this
     */
    public function disableFilter($filterType)
    {
        SubscriptionFilter::disableFilterForSubscription($this, $filterType);

        return $this;
    }

    /**
     * @param Price[] $prices
     * @param array $amountsPerPriceId
     * @return ProductsInSubscription[]
     */
    public function addProducts(array $prices, array $amountsPerPriceId)
    {
        $result = [];
        foreach ($prices as $priceId => $price) {
            $productInSubscription = ProductsInSubscription::firstOrNew([
                'subscription_id'   => $this->id,
                'product_id'        => $price->productId,
            ]);
            $productInSubscription->subscriptionId = $this->id;
            $productInSubscription->productId = $price->productId;
            $productInSubscription->priceId = $price->id;
            $productInSubscription->amount = $amountsPerPriceId[$priceId];
            $productInSubscription->save();
            $result[$priceId] = $productInSubscription;
        }

        return $result;
    }

    /**
     * @todo implement
     * @param Discount[] $discounts
     * @return array
     */
    public function addDiscounts(array $discounts)
    {
        return [];
    }

    /**
     * @param \DateTime|string $when
     * @return SubscriptionVeto[]
     */
    protected function getSubscriptionVetosForDate($when)
    {
        return SubscriptionVeto::findVetosForSubscriptionContainingDate($this, $when);
    }

}
