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
     * @param bool|int $subscriptionPeriod False for not preparing subscription
     * @param Delivery|null $delivery Null only when $subscriptionPeriod is empty
     * @return null|Subscription
     * @throws \InvalidArgumentException
     */
    public static function generateSubscription(Account $account, $subscriptionPeriod, Delivery $delivery = null )
    {
        if (empty($subscriptionPeriod)) {
            return null;
        }
        if (empty($delivery)) {
            throw new InvalidArgumentException('Subscription period specified but Delivery not');
        }
        $subscription = new Subscription();
        $subscription->accountId = $account->id;
        $subscription->status = 1;
        $subscription->period = $subscriptionPeriod;
        $subscription->start = $delivery->start;
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
            $productInSubscription = ProductsInSubscription::firstOrCreate([
                'subscription_id'   => $this->id,
                'product_id'        => $price->productId,
            ]);
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
