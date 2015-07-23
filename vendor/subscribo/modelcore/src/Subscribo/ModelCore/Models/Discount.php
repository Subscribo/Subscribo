<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Product;
use Subscribo\ModelCore\Models\Price;

/**
 * Model Discount
 *
 * Model class for being changed and used in the application
 */
class Discount extends \Subscribo\ModelCore\Bases\Discount
{
    /**
     * @todo implement
     * @param string|int $originalAmount
     * @param array $productWithPrices
     * @param string|int $amount
     * @param Product $product
     * @param Price $price
     * @return string|int
     */
    public function applyOnProductNetPrice($originalAmount, array $productWithPrices, $amount, Product $product, Price $price)
    {
        return $originalAmount;
    }

    /**
     * @todo implement
     * @param string|int $originalAmount
     * @param array $productWithPrices
     * @param string|int $amount
     * @param Product $product
     * @param Price $price
     * @return string|int
     */
    public function applyOnProductGrossPrice($originalAmount, array $productWithPrices, $amount, Product $product, Price $price)
    {
        return $originalAmount;
    }

    /**
     * @todo implement
     * @param string|int $originalAmount
     * @param array $productsWithPrices
     * @param array $amountsPerPriceIds
     * @param Product[] $products
     * @param Price[] $prices
     * @return string|int
     */
    public function applyOnTotalNetPrice($originalAmount, array $productsWithPrices, array $amountsPerPriceIds, array $products, array $prices)
    {
        return $originalAmount;
    }

    /**
     * @todo implement
     * @param string|int $originalAmount
     * @param array $productsWithPrices
     * @param array $amountsPerPriceIds
     * @param Product[] $products
     * @param Price[] $prices
     * @return string|int
     */
    public function applyOnTotalGrossPrice($originalAmount, array $productsWithPrices, array $amountsPerPriceIds, array $products, array $prices)
    {
        return $originalAmount;
    }



}
