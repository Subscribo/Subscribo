<?php namespace Subscribo\ModelCore\Models;

use RuntimeException;
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
     * @param \DateTime|string $start
     * @param DeliveryWindowType|int|null $deliveryWindowType
     * @param string $status
     * @return Subscription
     */
    public static function generate(
        Account $account,
        $currency,
        $country,
        $shippingAddress,
        $billingAddress = true,
        $start = 'today',
        $deliveryWindowType = null,
        $status = self::STATUS_ACTIVE
    ) {
        $serviceId = $account->serviceId;
        $shippingAddress = Address::provideForService($serviceId, $shippingAddress);
        if (true === $billingAddress) {
            $billingAddress = $shippingAddress;
        }
        $billingAddress = Address::provideForService($serviceId, $billingAddress);
        $startDate = DateTimeUtils::makeDate($start);

        $subscription = new Subscription();
        $subscription->serviceId = $serviceId;
        $subscription->account()->associate($account);
        $subscription->currency()->associate($currency);
        $subscription->country()->associate($country);
        $subscription->shippingAddress()->associate($shippingAddress);
        $subscription->billingAddress()->associate($billingAddress);
        $subscription->status = $status;
        $subscription->deliveryWindowType()->associate($deliveryWindowType);
        $subscription->start = $startDate;
        $subscription->save();

        return $subscription;
    }

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
     * @todo Refactoring: Merge with generate()
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
        $serviceId = $account->serviceId;
        $shippingAddress = Address::provideForService($serviceId, $shippingAddress);
        if (true === $billingAddress) {
            $billingAddress = $shippingAddress;
        }
        $billingAddress = Address::provideForService($serviceId, $billingAddress);
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

    public static function generateFromSalesOrder(SalesOrder $salesOrder, $start = true, $status = self::STATUS_ACTIVE)
    {
        if (true === $start) {
            $start = $salesOrder->delivery->start;
        }
        $deliveryWindowTypeId = $salesOrder->deliveryWindow ? $salesOrder->deliveryWindow->deliveryWindowTypeId : null;

        return static::generate(
            $salesOrder->account,
            $salesOrder->currencyId,
            $salesOrder->countryId,
            $salesOrder->shippingAddressId,
            $salesOrder->billingAddressId,
            $start,
            $deliveryWindowTypeId,
            $status
        );
    }

    public static function prepareSubscriptionFromSalesOrder(
        SalesOrder $salesOrder,
        $subscriptionFilterTypes = [],
        $discounts = [],
        $start = true,
        $status = self::STATUS_ACTIVE
    ) {
        $amountsPerPriceId = [];
        foreach ($salesOrder->realizationsInSalesOrders as $realizationInSalesOrder) {
            $amountsPerPriceId[$realizationInSalesOrder->priceId] = $realizationInSalesOrder->amount;
        }
        if (empty($amountsPerPriceId)) {
            throw new RuntimeException('No amounts per price id');
        }
        $subscription = static::generateFromSalesOrder($salesOrder, $start, $status);
        $appliedFilters = $subscription->applyFilters($subscriptionFilterTypes);
        $productsInSubscription = $subscription->addProducts($amountsPerPriceId);
        $discountsInSubscription = $subscription->addDiscounts($discounts, $salesOrder->discounts);

        return [
            'subscription' => $subscription,
            'appliedSubscriptionFilters' => $appliedFilters,
            'productsInSubscription' => $productsInSubscription,
            'discountsInSubscription' => $discountsInSubscription,
        ];
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
     * @param array $amountsPerPriceId
     * @return ProductsInSubscription[]
     */
    public function addProducts(array $amountsPerPriceId)
    {
        $result = [];
        foreach ($amountsPerPriceId as $priceId => $amount) {
            $productInSubscription = ProductsInSubscription::firstOrNew([
                'subscription_id'   => $this->id,
                'price_id'        => $priceId,
            ]);
            $productInSubscription->subscriptionId = $this->id;
            $productInSubscription->priceId = $priceId;
            $productInSubscription->amount = $amount;
            $productInSubscription->save();
            $result[$priceId] = $productInSubscription;
        }

        return $result;
    }

    /**
     * @param array $discounts
     * @param array $discountsFromSalesOrder
     * @return array
     */
    public function addDiscounts(array $discounts, $discountsFromSalesOrder = [])
    {
        return [];
    }

    /**
     * @param array $subscriptionFilterTypes
     * @return array
     */
    protected function applyFilters($subscriptionFilterTypes)
    {
        $appliedFilters = [];
        foreach ($subscriptionFilterTypes as $filterType) {
            $appliedFilters[] = $this->enableFilter($filterType);
        }

        return $appliedFilters;
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
