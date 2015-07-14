<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\TaxGroup;
use InvalidArgumentException;

/**
 * Model Product
 *
 * Model class for being changed and used in the application
 */
class Product extends \Subscribo\ModelCore\Bases\Product
{
    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @param int|null $currencyId
     * @return mixed
     */
    public static function findAllByServiceIdWithPrices($serviceId, $currencyId, $countryId = null)
    {
        if ($countryId) {
            $taxGroupSubQuery =  function ($query) use ($countryId) {
                    $query->where('country_id', $countryId)
                        ->orWhereNull('country_id');
                };
        } else {
            $taxGroupSubQuery =  function ($query) { $query->whereNull('country_id'); };
        }
        $priceSubQuery = function ($query) use ($currencyId, $countryId) {
            $query->where('currency_id', $currencyId);
            if ($countryId) {
                $query->where(function ($subQuery) use ($countryId) {
                    $subQuery->whereHas('countries', function ($subSubQuery) use ($countryId) {
                        $subSubQuery->where('countries.id', $countryId);
                    });
                    $subQuery->orWhere('everywhere', true);
                });
            } else {
                $query->where('everywhere', true);
            }
        };
        $mainQuery = static::withTranslations()->with([
            'prices',
            'taxCategory',
            'taxCategory.taxGroups' => $taxGroupSubQuery,
        ])->where('service_id', $serviceId)
            ->whereHas('prices', $priceSubQuery);
        $queryResult = $mainQuery->get();

        $result = [];
        foreach ($queryResult as $item) {
            $itemWithPrices = $item->toArrayWithPrices($currencyId, $countryId);
            if ($itemWithPrices) {
                $result[] = $itemWithPrices;
            }
        }

        return $result;
    }


    public function toArrayWithPrices($currencyId, $countryId = null)
    {
        $selectedPrices = [];
        foreach ($this->prices as $price) {
            if ($price->currencyId !== $currencyId) {
                continue;
            }
            if ($countryId and $price->isForCountryId($countryId)) {
                array_unshift($selectedPrices, $price);
            } elseif ($price->everywhere) {
                $selectedPrices[] = $price;
            }
        }
        if (empty($selectedPrices)) {

            return null;
        }
        $result = parent::toArray();
        $taxGroup = $taxGroup = TaxGroup::findByCategoryIdAndCountryId($this->taxCategoryId, $countryId);
        if (empty($taxGroup)) {

            throw new InvalidArgumentException('TaxGroup not found for given categoryId and countryId');
        }
        $taxPercent = $taxGroup->taxPercent ?: '0';
        /** @var \Subscribo\ModelCore\Models\Price $selectedPrice */
        $selectedPrice = reset($selectedPrices);
        $result['price_currency_id'] = $selectedPrice->currency->id;
        $result['price_currency_code'] = $selectedPrice->currency->code;
        $result['price_currency_symbol'] = $selectedPrice->currency->symbol;
        $result['price_net'] = $selectedPrice->calculateNetAmount($taxPercent);
        $result['price_gross'] = $selectedPrice->calculateGrossAmount($taxPercent);
        $result['tax_percent'] = $taxPercent;
        $result['tax_category_name'] = $this->taxCategory->name;
        $result['tax_category_short_name'] = $this->taxCategory->short_name;

        return $result;
    }
}
