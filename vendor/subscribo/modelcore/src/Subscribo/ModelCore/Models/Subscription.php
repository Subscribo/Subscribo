<?php namespace Subscribo\ModelCore\Models;

use InvalidArgumentException;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\Discount;
use Subscribo\ModelCore\Models\ProductsInSubscription;


/**
 * Model Subscription
 *
 * Model class for being changed and used in the application
 */
class Subscription extends \Subscribo\ModelCore\Bases\Subscription
{
    /**
     * @param Account $account
     * @param int $subscriptionPeriod
     * @param int|null $start (date)
     * @param int|null $deliveryWindowTypeId
     * @return Subscription
     */
    public static function generateSubscription(Account $account, $subscriptionPeriod, $start = null, $deliveryWindowTypeId = null)
    {

        $subscription = new Subscription();
        $subscription->accountId = $account->id;
        $subscription->status = 1;
        $subscription->period = $subscriptionPeriod;
        $subscription->deliveryWindowTypeId = $deliveryWindowTypeId;
        $subscription->start = $start;
        $subscription->save();

        return $subscription;
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

}
