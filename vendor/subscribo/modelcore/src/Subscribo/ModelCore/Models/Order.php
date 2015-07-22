<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Delivery;

use Subscribo\ModelCore\Models\Price;
use Subscribo\ModelCore\Models\Realization;
use Subscribo\ModelCore\Models\Subscription;
use Subscribo\ModelCore\Exceptions\ArgumentValidationException;

/**
 * Model Order
 *
 * Model class for being changed and used in the application
 */
class Order extends \Subscribo\ModelCore\Bases\Order
{
    public static function generateOrder(Account $account)
    {
        $order = new Order();
        $order->serviceId = $account->serviceId;
        $order->accountId = $account->id;

        $order->save();

        return $order;
    }


    public static function prepareOrder(Account $account, array $amountsPerPriceId, array $discountIds = [], $currencyId = null, $countryId = null, Delivery $delivery = null, $subscriptionPeriod = false)
    {
        $serviceId = $account->serviceId;
        $deliveryId = $delivery ? $delivery->id : null;
        $prices = static::checkPrices($serviceId, $amountsPerPriceId, $currencyId, $countryId);
        $products = static::checkProductsAndAmounts($amountsPerPriceId, $prices);
        $realizations = static::checkRealizations($serviceId, $prices, $deliveryId);
        $discounts = static::checkDiscounts($discountIds);
        $subscription = Subscription::generateSubscription($account, $subscriptionPeriod, $delivery);
        $productsInSubscription = $subscription ? $subscription->addProducts($amountsPerPriceId, $prices) : [];
        $discountsInSubscription = $subscription ? $subscription->addDiscounts($discounts) : [];

        $order = static::generateOrder($account);
        return [
            'order' => $order,
            'subscription' => $subscription,
            'productsInSubscription' => $productsInSubscription,
            'discountsInSubscription' => $discountsInSubscription,
        ];
    }

    protected static function checkPrices($serviceId, array $amountsPerPriceId, $currencyId = null, $countryId = null)
    {
        $prices = [];
        foreach ($amountsPerPriceId as $priceId => $amount) {
            $price = Price::find($priceId);
            if (empty($price)) {
                throw new ArgumentValidationException('priceNotFound', $priceId);
            }
            /** @var Price $price */
            if ($price->serviceId !== $serviceId) {
                throw new ArgumentValidationException('invalidService', $priceId);
            }
            if ($currencyId) {
                if ($price->currencyId !== $currencyId) {
                    throw new ArgumentValidationException('invalidCurrency', $priceId);
                }
            } else {
                $currencyId = $price->currencyId;
            }
            if ($countryId and ( ! $price->everywhere) and ( ! $price->isForCountryId($countryId))) {
                throw new ArgumentValidationException('invalidCountry', $priceId);
            }

            $prices[$priceId] = $price;
        }

        return $prices;
    }


    protected static function checkProductsAndAmounts(array $amountsPerPriceId, array $prices)
    {
        $products = [];
        foreach ($prices as $priceId => $price)
        {
            $product = $price->product;
            if (empty($product)) {
                throw new ArgumentValidationException('productNotFound', $priceId);
            }
            $amount = $amountsPerPriceId[$priceId];
            if (( ! $product->checkAmount($amount))) {
                throw new ArgumentValidationException('amountInvalid', $priceId, ['%amount%' => $amount]);
            }
            $products[$priceId] = $product;
        }

        return $products;
    }


    protected static function checkRealizations($serviceId, array $prices, $deliveryId = null)
    {
        $realizations = [];
        foreach ($prices as $priceId => $price)
        {
            $product = $price->product;
            $realization = Realization::findByAttributes($serviceId, $product->id, $deliveryId, true);
            if (empty($realization)) {
                throw new ArgumentValidationException('noRealization', $priceId);
            }
            $realizations[$price->id] = $realization;
        }

        return $realizations;
    }

    /**
     * @todo implement
     * @param array $discountIds
     * @return Discount[]
     */
    protected static function checkDiscounts(array $discountIds)
    {
        return [];
    }
}
