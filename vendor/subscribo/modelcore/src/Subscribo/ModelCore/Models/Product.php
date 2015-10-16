<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Country;
use Subscribo\ModelCore\Models\Price;
use Subscribo\ModelCore\Models\SubscriptionPlan;
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
     * @param int|SubscriptionPlan $subscriptionPlan
     * @param int|null $countryId
     * @param int|null $currencyId
     * @return array
     */
    public static function findAllBySubscriptionPlanWithPrices($subscriptionPlan, $currencyId, $countryId = null)
    {
        $subscriptionPlanId = ($subscriptionPlan instanceof SubscriptionPlan)
            ? $subscriptionPlan->id : $subscriptionPlan;
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
        ])->where('subscription_plan_id', $subscriptionPlanId)
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
     * @param int|string|Country|null $country
     * @return null|TaxGroup
     * @throws \InvalidArgumentException
     */
    public function acquireTaxGroup($country = null)
    {
        if (is_null($country)) {
            $countryId = null;
        } elseif (is_numeric($country)) {
            $countryId = $country;
        } elseif ($country instanceof Country) {
            $countryId = $country->id;
        } elseif (is_string($country)) {
            $countryInstance = Country::findByIdentifier($country);
            if (empty($countryInstance)) {
                throw new InvalidArgumentException('Specified country not found');
            }
            $countryId = $countryInstance->id;
        } else {
            throw new InvalidArgumentException('Country argument invalid');
        }

        return TaxGroup::findByCategoryIdAndCountryId($this->taxCategoryId, $countryId);
    }

    /**
     * @param Price $price
     * @param int|string|Country|null $country
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function toArrayWithPrice(Price $price, $country = null)
    {
        $result = parent::toArray();
        $taxGroup = $this->acquireTaxGroup($country);
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
     * @param Price $price
     * @param int|string|Country|null $country
     * @param int|string $amount
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function toArrayWithPriceAndAmount(Price $price, $country = null, $amount = 1)
    {
        $result = $this->toArrayWithPrice($price, $country);
        $amount = strval($amount);
        $result['amount'] = $amount;
        $precision = $price->currency->precision;
        $result['total_price_net'] = bcmul($amount, $result['price_net'], $precision);
        $result['total_price_gross'] = bcmul($amount, $result['price_gross'], $precision);

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
