<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Price;
use Subscribo\ModelCore\Models\TaxGroup;
use InvalidArgumentException;
use Subscribo\ModelCore\Traits\SearchableByIdentifierAndServiceTrait;

/**
 * Model Product
 *
 * Model class for being changed and used in the application
 * @method \Subscribo\ModelCore\Models\Product findByIdentifierAndService() static findByIdentifierAndService(int|string $identifier, int|null|Service $service, bool $alsoCommon)
 */
class Product extends \Subscribo\ModelCore\Bases\Product
{
    use SearchableByIdentifierAndServiceTrait;

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
        /** @var Product $item */
        foreach ($queryResult as $item) {
            $itemWithPrices = $item->toArrayWithAppropriatePrice($currencyId, $countryId);
            if ($itemWithPrices) {
                $result[] = $itemWithPrices;
            }
        }

        return $result;
    }

    /**
     * @param int $currencyId
     * @param int| null $countryId
     * @return Price|null
     */
    public function findAppropriatePrice($currencyId, $countryId = null)
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
        return $selectedPrices ? reset($selectedPrices) : null;
    }

    /**
     * @param Price $price
     * @param int|null $countryId
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function toArrayWithPrice(Price $price, $countryId = null)
    {
        $result = parent::toArray();
        $taxGroup = $taxGroup = TaxGroup::findByCategoryIdAndCountryId($this->taxCategoryId, $countryId);
        if (empty($taxGroup)) {

            throw new InvalidArgumentException('TaxGroup not found for given categoryId and countryId');
        }
        $taxPercent = $taxGroup->taxPercent ?: '0';
        $result['price_id'] = $price->id;
        $result['price_currency_id'] = $price->currency->id;
        $result['price_currency_code'] = $price->currency->code;
        $result['price_currency_symbol'] = $price->currency->symbol;
        $result['price_net'] = $price->calculateNetAmount($taxPercent);
        $result['price_gross'] = $price->calculateGrossAmount($taxPercent);
        $result['tax_percent'] = $taxPercent;
        $result['tax_category_name'] = $this->taxCategory->name;
        $result['tax_category_short_name'] = $this->taxCategory->short_name;

        return $result;
    }

    /**
     * @param int $currencyId
     * @param int|null $countryId
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function toArrayWithAppropriatePrice($currencyId, $countryId = null)
    {
        $price = $this->findAppropriatePrice($currencyId, $countryId);

        return $price ? $this->toArrayWithPrice($price, $countryId) : null;
    }

    public function checkAmount($amount) {
        if (is_int($amount)) {
            return true;
        }
        return ctype_digit($amount);
    }
}
