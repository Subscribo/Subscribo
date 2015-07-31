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
use Subscribo\ModelCore\Models\RealizationsInSalesOrder;
use Subscribo\ModelCore\Models\Subscription;
use Subscribo\ModelCore\Exceptions\ArgumentValidationException;
use Subscribo\ModelBase\Traits\HavingHashTrait;

/**
 * Model SalesOrder
 *
 * Model class for being changed and used in the application
 */
class SalesOrder extends \Subscribo\ModelCore\Bases\SalesOrder
{
    use HavingHashTrait;

    const STATUS_NOT_APPLICABLE = null;

    public static function generateSalesOrder(
        Account $account,
        $currencyId,
        Address $shippingAddress = null,
        Delivery $delivery = null,
        DeliveryWindow $deliveryWindow = null,
        Subscription $subscription = null,
        $type = self::TYPE_MANUAL,
        $status = self::STATUS_ORDERING,
        $anticipatedDeliveryStart = true,
        $anticipatedDeliveryEnd = true
    ) {
        if (true === $anticipatedDeliveryStart) {
            $anticipatedDeliveryStart = null; //todo calculate from Delivery and DeliveryWindow
        }
        if (true === $anticipatedDeliveryEnd) {
            $anticipatedDeliveryEnd = null; //todo calculate from Delivery and DeliveryWindow
        }
        $salesOrder = static::makeWithHash();
        $salesOrder->currencyId = $currencyId;
        $salesOrder->serviceId = $account->serviceId;
        $salesOrder->accountId = $account->id;
        $salesOrder->type = $type;
        $salesOrder->status = $status;
        $salesOrder->deliveryId = $delivery ? $delivery->id : null;
        $salesOrder->deliveryWindowId = $deliveryWindow ? $deliveryWindow->id : null;
        $salesOrder->subscriptionId = $subscription ? $subscription->id : null;
        $salesOrder->anticipatedDeliveryStart = $anticipatedDeliveryStart;
        $salesOrder->anticipatedDeliveryEnd = $anticipatedDeliveryEnd;
        $salesOrder->shippingAddressId = $shippingAddress ? $shippingAddress->id : null;
        $salesOrder->save();

        return $salesOrder;
    }


    public static function prepareSalesOrder(
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
            $countryId = $shippingAddress ? $shippingAddress->countryId : null;
        }
        $serviceId = $account->serviceId;
        $deliveryId = $delivery ? $delivery->id : null;
        $prices = static::checkPrices($serviceId, $amountsPerPriceId, $currencyId, $countryId);
        if (empty($prices)) {
            throw new InvalidArgumentException('No prices provided');
        }
        $currencyId = $currencyId ?: reset($prices)->currencyId;
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
        $salesOrder = static::generateSalesOrder($account, $currencyId, $shippingAddress, $delivery, $deliveryWindow, $subscription, $type, $status, $anticipatedDeliveryStart, $anticipatedDeliveryEnd);
        $realizationsInSalesOrder = $salesOrder->addRealizations($realizations, $amountsPerPriceId);
        $discountsInSalesOrder = $salesOrder->addDiscounts($discounts);
        $result = static::calculateSums($amountsPerPriceId, $prices, $products, $discountsInSalesOrder, $countryId);
        $salesOrder->netSum = $result['netSum'];
        $salesOrder->grossSum = $result['grossSum'];
        $salesOrder->save();
        $result['salesOrder'] = $salesOrder;
        $result['subscription'] = $subscription;
        $result['prices'] = $prices;
        $result['products'] = $products;
        $result['realizations'] = $realizations;
        $result['productsInSubscription'] = $productsInSubscription;
        $result['discountsInSubscription'] = $discountsInSubscription;
        $result['realizationsInSalesOrder'] = $realizationsInSalesOrder;
        $result['discountsInSalesOrder'] = $discountsInSalesOrder;

        return $result;
    }

    /**
     * @param Realization[] $realizations This array should have the same keys as amountsPerPriceId array
     * @param array $amountsPerPriceId
     * @return RealizationsInSalesOrder[]
     */
    public function addRealizations(array $realizations, array $amountsPerPriceId)
    {
        $result = [];
        foreach ($realizations as $priceId => $realization) {
            $realizationInSalesOrder = RealizationsInSalesOrder::firstOrNew([
                'sales_order_id'   => $this->id,
                'realization_id' => $realization->id,
            ]);
            $realizationInSalesOrder->salesOrderId = $this->id;
            $realizationInSalesOrder->realizationId = $realization->id;
            $realizationInSalesOrder->priceId = $priceId;
            $realizationInSalesOrder->amount = $amountsPerPriceId[$priceId];
            $realizationInSalesOrder->save();
            $result[$priceId] = $realizationInSalesOrder;
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
     * @throws \Subscribo\ModelCore\Exceptions\ArgumentValidationException
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
            try {
                $productWithPrices = $product->toArrayWithPrice($price, $countryId);
            } catch (InvalidArgumentException $e) {
                throw new ArgumentValidationException('taxGroupNotFound', $priceId);
            }
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
