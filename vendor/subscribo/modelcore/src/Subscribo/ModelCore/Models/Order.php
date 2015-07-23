<?php

namespace Subscribo\ModelCore\Models;

use InvalidArgumentException;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\DeliveryWindow;
use Subscribo\ModelCore\Models\Discount;
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
    const TYPE_AUTOMATIC = 'automatic';
    const TYPE_MANUAL = 'manual';

    const STATUS_NOT_APPLICABLE = 0;
    const STATUS_ORDERING = 1;
    const STATUS_ORDERED = 2;
    const STATUS_PREPARED = 3;
    const STATUS_SENT = 4;
    const STATUS_DELIVERED = 5;
    const STATUS_RETURNED = 6;
    const STATUS_CANCELLED = 7;

    public static function generateOrder(
        Account $account,
        Address $shippingAddress = null,
        Delivery $delivery = null,
        DeliveryWindow $deliveryWindow = null,
        Subscription $subscription = null,
        $type = self::TYPE_MANUAL,
        $status = self::STATUS_ORDERING,
        $anticipatedDeliveryStart = true,
        $anticipatedDeliveryEnd = true,
        $transactionId = null
    ) {
        if (true === $anticipatedDeliveryStart) {
            $anticipatedDeliveryStart = null; //todo calculate from Delivery and DeliveryWindow
        }
        if (true === $anticipatedDeliveryEnd) {
            $anticipatedDeliveryEnd = null; //todo calculate from Delivery and DeliveryWindow
        }
        $order = new Order();
        $order->serviceId = $account->serviceId;
        $order->accountId = $account->id;
        $order->type = $type;
        $order->status = $status;
        $order->transactionId = $transactionId;
        $order->deliveryId = $delivery ? $delivery->id : null;
        $order->deliveryWindowId = $deliveryWindow ? $deliveryWindow->id : null;
        $order->subscriptionId = $subscription ? $subscription->id : null;
        $order->anticipatedDeliveryStart = $anticipatedDeliveryStart;
        $order->anticipatedDeliveryEnd = $anticipatedDeliveryEnd;
        $order->shippingAddressId = $shippingAddress ? $shippingAddress->id : null;
        $order->save();

        return $order;
    }


    public static function prepareOrder(
        Account $account,
        array $amountsPerPriceId,
        array $discountIds = [],
        Delivery $delivery = null,
        DeliveryWindow $deliveryWindow = null,
        $subscriptionPeriod = false,
        Address $shippingAddress = null,
        $currencyId = null,
        $countryId = true,
        $type = self::TYPE_MANUAL,
        $status = self::STATUS_ORDERING,
        $anticipatedDeliveryStart = true,
        $anticipatedDeliveryEnd = true
    ) {
        if (true === $countryId) {
            $countryId = $shippingAddress ? $shippingAddress->id : null;
        }
        $serviceId = $account->serviceId;
        $deliveryId = $delivery ? $delivery->id : null;
        $prices = static::checkPrices($serviceId, $amountsPerPriceId, $currencyId, $countryId);
        $products = static::checkProductsAndAmounts($amountsPerPriceId, $prices);
        $realizations = static::checkRealizations($serviceId, $prices, $deliveryId);
        $discounts = static::checkDiscounts($discountIds);
        if ($subscriptionPeriod) {
            if (empty($delivery)) {
                throw new InvalidArgumentException('Subscription period specified but Delivery not');
            }
            $deliveryWindowTypeId = $deliveryWindow ? $deliveryWindow->deliveryWindowTypeId : null;
            $subscription = Subscription::generateSubscription($account, $subscriptionPeriod, $delivery->start, $deliveryWindowTypeId);
        } else {
            $subscription = null;
        }
        $productsInSubscription = $subscription ? $subscription->addProducts($prices, $amountsPerPriceId) : [];
        $discountsInSubscription = $subscription ? $subscription->addDiscounts($discounts) : [];
        $order = static::generateOrder($account, $shippingAddress, $delivery, $deliveryWindow, $subscription, $type, $status, $anticipatedDeliveryStart, $anticipatedDeliveryEnd, null);
        $realizationsInOrder = $order->addRealizations($realizations, $amountsPerPriceId);
        $discountsInOrder = $order->addDiscounts($discounts);
        $result = static::calculateSums($amountsPerPriceId, $prices, $products, $discountsInOrder, $countryId);
        $result['order'] = $order;
        $result['currencyId'] = $currencyId ?: ($prices ? reset($prices)->currencyId : null);
        $result['subscription'] = $subscription;
        $result['prices'] = $prices;
        $result['products'] = $products;
        $result['realizations'] = $realizations;
        $result['productsInSubscription'] = $productsInSubscription;
        $result['discountsInSubscription'] = $discountsInSubscription;
        $result['realizationsInOrder'] = $realizationsInOrder;
        $result['discountsInOrder'] = $discountsInOrder;

        return $result;
    }

    /**
     * @param Realization[] $realizations This array should have the same keys as amountsPerPriceId array
     * @param array $amountsPerPriceId
     * @return RealizationsInOrder[]
     */
    public function addRealizations(array $realizations, array $amountsPerPriceId)
    {
        $result = [];
        foreach ($realizations as $priceId => $realization) {
            $realizationInOrder = RealizationsInOrder::firstOrNew([
                'order_id'   => $this->id,
                'realization_id' => $realization->id,
            ]);
            $realizationInOrder->orderId = $this->id;
            $realizationInOrder->realizationId = $realization->id;
            $realizationInOrder->priceId = $priceId;
            $realizationInOrder->amount = $amountsPerPriceId[$priceId];
            $realizationInOrder->save();
            $result[$priceId] = $realizationInOrder;
        }

        return $result;
    }

    /**
     * @todo implement
     * @param Discount[] $discounts
     * @return Discount[]
     */
    public function addDiscounts(array $discounts)
    {
        return [];
    }

    /**
     * @param $serviceId
     * @param array $amountsPerPriceId
     * @param null $currencyId
     * @param null $countryId
     * @return Price[]
     * @throws \Subscribo\ModelCore\Exceptions\ArgumentValidationException
     */
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

    /**
     * @todo add discounts
     *
     * @param array $amountsPerPriceId
     * @param Price[] $prices
     * @param Product[] $products
     * @param Discount[] $discountsInOrder
     * @param int|null $countryId
     * @return array
     */
    protected static function calculateSums(array $amountsPerPriceId, array $prices, array $products, array $discountsInOrder = [], $countryId = null)
    {
        $netSum = '0';
        $grossSum = '0';
        $productsWithPrices = [];
        $precision = $prices ? (reset($prices)->currency->precision) : 2;
        foreach ($amountsPerPriceId as $priceId => $amount) {
            $amountString = strval($amount);
            $price = $prices[$priceId];
            $product = $products[$priceId];
            $productWithPrices = $product->toArrayWithPrice($price, $countryId);
            $productsWithPrices[$priceId] = $productWithPrices;
            $productNetPrice = $productWithPrices['price_net'];
            $productGrossPrice = $productWithPrices['price_gross'];
            $productNetPriceMultiplied = bcmul($amountString, $productNetPrice, $precision);
            $productGrossPriceMultiplied = bcmul($amountString, $productGrossPrice, $precision);
            $discountedNetPrice = $productNetPriceMultiplied;
            $discountedGrossPrice = $productGrossPriceMultiplied;
            foreach ($discountsInOrder as $discount) {
                $discountedNetPrice = $discount->applyOnProductNetPrice($discountedNetPrice, $productWithPrices, $amountString, $product, $price);
                $discountedGrossPrice = $discount->applyOnProductNetPrice($discountedGrossPrice, $productWithPrices, $amountString, $product, $price);
            }
            $netSum = bcadd($netSum, $discountedNetPrice, $precision);
            $grossSum = bcadd($grossSum, $discountedGrossPrice, $precision);
        }
        foreach ($discountsInOrder as $discount) {
            $netSum = $discount->applyOnTotalNetPrice($netSum, $productsWithPrices, $amountsPerPriceId, $products, $prices);
            $grossSum = $discount->applyOnTotalGrossPrice($grossSum, $productsWithPrices, $amountsPerPriceId, $products, $prices);
        }

        return [
            'netSum' => $netSum,
            'grossSum' => $grossSum,
            'productsWithPrices' => $productsWithPrices,
        ];
    }
}
